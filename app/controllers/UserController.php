<?php

namespace app\controllers;

use app\models\User;

class UserController extends AppController {

    public $layout = 'default';

    public function signupAction() {
        if (!empty($_POST)) {
            $user = new User();
            $data = $_POST;
            $user->load($data);
            if (!$user->validate($data) || !$user->checkUnique()) {
                $user->getErrors();
                $_SESSION['form_data'] = $data;
            } else {
                $user->attributes['password'] = password_hash($user->attributes['password'], PASSWORD_DEFAULT);
                if ($user->save('user')) {
                    $_SESSION['success'] = 'Пользователь зарегистрирован';
                    redirect('/user/login');
                } else {
                    $_SESSION['errors'] = 'Возникла ошибка сохранения данных в БД';
                }
            }
            redirect();
        }
        /*if (!empty($_FILES)) {
            debug($_FILES);
        }*/
        //debug($user);
        // формируем метатеги для страницы
        $this->setMeta('Регистрация нового пользователя');

    }

    public function loginAction() {
        // проверяем приходят на страницу какие-нибудь данные или нет
        if (!empty($_POST)) {
            // если данные пришли, создаем объект пользователя
            $user = new User();
            if ($user->login()) {
                $_SESSION['success'] = 'Вы успешно авторизованы';
                redirect('/');
            } else {
                $_SESSION['errors'] = 'Логин/Пароль введены не верно';
                redirect();
            }

        }
        // формируем метатеги для страницы
        $this->setMeta('Авторизация пользователя');
    }

    public function logoutAction() {
        if (isset($_SESSION['user'])) unset($_SESSION['user']);
        redirect();
    }

}