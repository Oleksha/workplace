<?php

namespace workplace\base;

/**
 * Class Controller - базовый класс код из которого будет выполняться во
 * всех других контроллерах чере контролеер приложения AppController
 * @package workplace\base
 */
abstract class Controller {

    public $route; // содержит массив всех данных о текущем маршруте
    public $controller;
    public $model;
    public $view;
    public $layout;
    public $prefix;
    public $data = [];  // данные которые будут передаваться из контроллера в вид
    public $meta = ['title' => '', 'description' => '', 'keywords' => ''];  // метаданные которые будут передаваться из контроллера в вид

    public function __construct($route) {
        $this->route = $route;
        $this->controller = $route['controller'];
        $this->model = $route['controller'];
        $this->view = $route['action'];
        $this->prefix = $route['prefix'];
    }

    /**
     * @return mixed
     */
    public function getView() {
        $viewObject = new View($this->route, $this->layout, $this->view, $this->meta);
        $viewObject->render($this->data);
    }

    /**
     * @param array $data
     */
    public function set($data) {
        $this->data = $data;
    }

    /**
     * @param array $meta
     */
    public function setMeta($title = '', $desc = '', $keywords = '') {
        $this->meta['title'] = $title;
        $this->meta['description'] = $desc;
        $this->meta['keywords'] = $keywords;
    }

    public function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * @param $view string самм вид который требуется загрузить
     * @param $vars array набор параметров для передачи в вид
     * @return void загружает указанный вид
     */
    public function loadView($view, $vars = []) {
        extract($vars);
        require APP . "/views/{$this->prefix}{$this->controller}/{$view}.php";
        die; // завершаем работу программы
    }

}