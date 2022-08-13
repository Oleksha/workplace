<?php

namespace workplace;

class Db {

    use TSingletone;

    private function __construct() {
        // получаем данны для подключения БД
        $db = require_once CONF . '/config_db.php';
        class_alias('\RedBeanPHP\R', '\R');
        \R::setup($db['dsn'], $db['user'], $db['pass']);
        // проверяем получилось ли установить соединение с БД
        if (!\R::testConnection()) {
            // если нет - ошибка
            throw new \Exception("Нет соединения с БД", 500);
        }
        \R::freeze(true);
        if (DEBUG) {
            \R::debug(true, 1);
        }
    }

}