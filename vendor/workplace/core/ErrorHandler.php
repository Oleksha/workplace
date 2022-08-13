<?php

namespace workplace;

class ErrorHandler {

    public function __construct() {
        if (DEBUG) {
            // если включен режим разработки показываем все ошибки
            error_reporting(-1);
        } else {
            // иначе ошибки не показываем
            error_reporting(0);
        }
        // обрабатываем ошибки
        set_exception_handler([$this, 'exceptionHandler']);
    }

    // метод обрабатывающий перехваченные исключения
    public function exceptionHandler($e) {
        $this->logErrors($e->getMessage(), $e->getFile(), $e->getLine());
        $this->displayError('Исключение', $e->getMessage(), $e->getFile(), $e->getLine(), $e->getCode());
    }

    // метод для логирования ошибок
    protected function logErrors($message = '', $file = '', $line = '') {
        error_log("[" . date('Y-m-d H:i:s') . "] Текст ошибки: {$message} | Файл: {$file} | Строка: {$line}\n--------------------\n", 3, ROOT . '/tmp/errors.log');
    }

    // метод показывающий ошибку
    protected function displayError($errno, $errstr, $errfile, $errline, $responce = 404) {
        http_response_code($responce);
        if ($responce == 404 && !DEBUG) {
            require WWW . '/errors/404.php';
            die;
        }
        if (DEBUG) {
            require WWW . '/errors/dev.php';
        } else {
            require WWW . '/errors/prod.php';
        }
        die;
    }

}