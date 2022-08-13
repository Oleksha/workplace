<?php

namespace workplace\base;

/**
 * Class View
 * @package workplace\base
 */
class View {

    /**
     * текущий маршрут и параметры (controller, action, params)
     * @var array
     */
    public $route;
    /**
     * текущий controller
     * @var string
     */
    public $controller;
    public $model;
    /**
     * текущий вид
     * @var string
     */
    public $view;
    /**
     * текущий шаблон
     * @var string
     */
    public $layout;
    public $prefix;
    public $data = [];  // данные которые будут передаваться из контроллера в вид
    public $meta = [];  // метаданные которые будут передаваться из контроллера в вид
    /**
     * свойство для хранения вырезаемых скриптов
     * @var array
     */
    public $scripts = [];

    public function __construct($route, $layout = '', $view = '', $meta) {
        $this->route = $route;
        $this->controller = $route['controller'];
        $this->model = $route['controller'];
        $this->view = $view;
        $this->prefix = $route['prefix'];
        $this->meta = $meta;
        if ($layout === false) {
            // Если шаблон не нужен
            $this->layout = false;
        } else {
            // если передано значение шаблона берем его,
            // инначе берем шаблон по умолчанию из настроек
            $this->layout = $layout ?: LAYOUT;
        }
    }

    /**
     * @param $data
     * метод формирующий страницу на основании данных $data
     */
    public function render($data) {
        // проверяем переданные данные являются массивом
        if (is_array($data)) extract($data);
        // формируем путь к view
        $viewFile = APP . "/views/{$this->prefix}{$this->controller}/{$this->view}.php";
        // проверяем существует ли файл
        if (is_file($viewFile)) {
            // если файл найден подключаем его, но не выводим сразу, а используем буфферизацию
            ob_start();
            require_once $viewFile;
            // поместим все что загрузили в переменную $content и очистим буффер
            $content = ob_get_clean();
        } else {
            // если файла нет ошибка
            throw new \Exception("Файл вида $viewFile не найден", 500);
        }
        if (false !== $this->layout) {
            // если мы собираемя выводить шаблон
            // формируем путь к layout
            $layoutFile = APP . "/views/layouts/{$this->layout}.php";
            // проверяем существует ли файл
            if (is_file($layoutFile)) {
                // если файл найден вырезаем скрипты
                $content = $this->getScript($content);
                $scripts = []; //для хранения скриптов
                if (!empty($this->scripts[0])) {
                    // если скрипты найдены преобразуем массив в одномерный
                    $scripts = $this->scripts[0];
                }
                // подключаем его
                require_once $layoutFile;
            } else {
                // если файла нет ошибка
                throw new \Exception("Шаблона {$this->layout} не найден", 500);
            }
        }
    }

    /**
     * Метод формирующий метатеги
     * @return array
     */
    public function getMeta() {
        $output = '<title>' . $this->meta['title'] . '</title>' . PHP_EOL;
        $output .= '    <meta name="description" content="' . $this->meta['description'] . '">' . PHP_EOL;
        $output .= '    <meta name="keywords" content="' . $this->meta['keywords'] . '">' . PHP_EOL;
        return $output;
    }

    /**
     * Находит и вырезает все касающееся скриптов из вида
     * @param $content string html-текст вида
     * @return string html-текст вида без скриптов
     */
    protected function getScript($content) {
        // переменная для хранения шаблона вдля поиска ивыпезания скриптов
        $pattern = "#<script.*?>.*?</script>#si";
        preg_match_all($pattern, $content, $this->scripts);
        if (!empty($this->scripts)) {
            // если скрипты найдены вырезаем их из вида
            $content = preg_replace($pattern, '', $content);
        }
        return $content;
    }

}