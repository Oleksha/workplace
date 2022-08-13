<?php

namespace app\models;

use RedBeanPHP\R;

class User extends AppModel {

    /**
     * Содержит поля таблицы User
     * @var string массив
     */
    public $attributes = [
        'login' => '',
        'password' => '',
        'name' => '',
        'email' => '',
        'avatar' => '',
    ];

    /**
     * Содержит правила проверки формы
     * @var string многомерный массив
     */
    public $rules = [
        'required' => [
            ['login'],
            ['password'],
            ['name'],
            ['email'],
        ],
        'email' => [
            ['email'],
        ],
        'equals' => [
            ['password', 'password_confirm']
        ]
    ];

    /**
     * Проверяет наличие имеющихся КА с такими inn или alias
     * @return bool TRUE если login и email свободны, и FALSE если заняты
     */
    public function checkUnique(): bool {
        // попытаемся найти в БД пользователя с таким login или email
        $user = R::getRow("SELECT * FROM user WHERE login = ? OR email = ?", [$this->attributes['login'], $this->attributes['email']]);
        if ($user) {
            // если нашли такую запись
            if ($user['login'] == $this->attributes['login']) {
                // совпадает inn
                $this->errors['unique'][] = "Логин ({$user['login']}) уже занят";
            }
            if ($user['email'] == $this->attributes['email']) {
                // совпадает alias
                $this->errors['unique'][] = "Email ({$user['email']}) уже занят";
            }
            return false;
        }
        return true;
    }

    /**
     * Функция авторизации пользователя
     * @param $isAdmin bool Администратор или Пользователь
     * @return bool
     */
    public function login(bool $isAdmin = false): bool {
        // получаем введенные данные пользователя
        $login = !empty(trim($_POST['login'])) ? trim($_POST['login']) : null;
        $password = !empty(trim($_POST['password'])) ? trim($_POST['password']) : null;
        if ($login && $password) {
            // если есть и логин и пароль
            if ($isAdmin) {
                // авторизация администратора
                // попытаемся найти в БД пользователя с таким login и статусом Администратор
                $user = R::getRow("SELECT * FROM user WHERE login = ? AND role = 'admin' LIMIT 1", [$login]);
            } else {
                // авторизация обычного пользователя
                // попытаемся найти в БД пользователя с таким login и статусом Администратор
                $user = R::getRow("SELECT * FROM user WHERE login = ? LIMIT 1", [$login]);
            }
            if ($user) {
                // если пользователь найден проверяем пароль
                if (password_verify($password, $user['password'])) {
                    // если пароль совпадает авторизуем пользователя
                    foreach ($user as $k => $v) {
                        // записываем в сессию все данные о пользователе кроме пароля
                        if ($k != 'password') $_SESSION['user'][$k] = $v;
                    }
                    return true;
                }
            }
        }
        return false;
    }

}