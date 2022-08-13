<?php

namespace workplace;

class App {

    public static $app;

    public function __construct() {
        $query = trim($_SERVER['QUERY_STRING'], '/');
        session_start();
        self::$app = Registry::instance();
        $this->getParams();
        new ErrorHandler();
        Router::dispatch($query);
    }

    protected function getParams() {
        // Получаем массив с параметрами
        $params = require_once  CONF . '/params.php';
        // Если массив не пуст
        if (!empty($params)) {
            foreach ($params as $k => $v) {
                self::$app->setProperty($k, $v);
            }
        }
    }

}