<?php

namespace workplace\base;

// класс отвечает за работу с данными
use Valitron\Validator;
use workplace\Db;

abstract class Model {

    public $attributes = []; // хранит массив свойств модели (идентичен полям базы данных)
    public $errors = []; // хранит массив возникших ошибок
    public $rules = []; // хранит правила проверки данных

    public function __construct() {
        // подключение к базе данных
        Db::instance();
    }

    /**
     * Автоматическая загрузка данных из форм ввода
     * @param $data array полученный набор данных
     */
    public function load($data) {
        // проходим по всем атрибутам
        foreach ($this->attributes as $name => $value) {
            // если в переданных данных data есть имя ключа атрибута
            if (isset($data[$name])) {
                // запоминаем в атрибуте соответсвующее значение
                $this->attributes[$name] = $data[$name];
            }
        }
    }

    /**
     * Сохраняет данные в БД
     * @param $table string Имя таблицы в которой будут сохранены данные
     * @return int 0 если произошла ошибка, и ID новой записи если все хорошо
     */
    public function save(string $table) {
        $tbl = \R::dispense($table); // подключаем источник данных table
        foreach ($this->attributes as $name => $value) {
            // проходим по всем атрибутам содержащим данные для добавления
            $tbl->$name = $value;
        }
        return \R::store($tbl);
    }

    public function edit($table, $id) {
        $tbl = \R::load($table, $id); // подключаем источник данных table
        foreach ($this->attributes as $name => $value) {
            // проходим по всем атрибутам содержащим данные для добавления
            $tbl->$name = $value;
        }
        return \R::store($tbl);
    }

    /**
     * Проверка корректности переданных данных согласно установленным правилам
     * @param $data array полученный набор данных
     * @return boolean TRUE если проверка пройдена, и FALSE если нет
     */
    public function validate($data) {
        // Русифицируем сообщения валидатора
        Validator::lang('ru');
        // создаем объект установленного vlucas/valitron
        $v = new Validator($data);
        // передаем ему массив установленных нами правил
        $v->rules($this->rules);
        if ($v->validate()) {
            // если проверка пройдена
            return true;
        }
        // запоминаем ошибки
        $this->errors = $v->errors();
        // проверка не пройдена
        return false;
    }

    /**
     * Формирует HTML-код вывода ошибок проверки заполненных полей формы
     * @return void
     */
    public function getErrors() {
        $errors = '<ul>';
        foreach ($this->errors as $error) {
            // проходимся в цикле по всем полям для заполнения и получаем массив ошибок в каждом
            foreach ($error as $item) {
                // выводим каждую ошибку для каждого поля формы
                $errors .= "<li>$item</li>";
            }
        }
        $errors .= '</ul>';
        // записываем оформленный HTML-код в сессию
        $_SESSION['errors'] = $errors;
    }

}