<?php

namespace workplace;

class Router {

    protected static $routes = []; // здесь хранится таблица всех маршрутов
    protected static $route = []; // здесь хранится текущий маршрут

    // добавляет маршрут в таблицу маршрутов
    public static function add($regexp, $route = []) {
        self::$routes[$regexp] = $route;
    }

    // метод возвращает таблицу маршрутов
    public static function getRoutes() {
        return self::$routes;
    }

    // метод возвращающий текущий маршрут
    public static function getRoute() {
        return self::$route;
    }

    public static function dispatch($url) {
        $url = self::removeQueryString($url);
        if (self::matchRoute($url)) {
            $controller = 'app\controllers\\' . self::$route['prefix'] . self::$route['controller'] . 'Controller';
            // проверяем сущществует ли класс Контроллера
            if (class_exists($controller)) {
                // если класс существует создаем объект каласса
                // и передаем в него параметры (текущий маршрут)
                $controllerObject = new $controller(self::$route);
                $action = self::lowerCamelCase(self::$route['action']) . 'Action';
                // Проверяем сущаствует ли такой метод в классе Контроллера
                if (method_exists($controllerObject, $action)) {
                    // если метод существует вызываем его
                    $controllerObject->$action();
                    $controllerObject->getView();
                } else {
                    // если метод не существует
                    throw new \Exception("Метод $controller::$action не найден", 400);
                }
            } else {
                // если класса нет ошибка
                throw new \Exception("Контроллер $controller не найден", 400);
            }
        } else {
            throw new \Exception("Страница не найдена", 404);
        }
    }

    /**
     * @param $url
     * @return bool
     * метод ищет соответствие адреса в таблице маршрутов
     */
    public static function matchRoute($url) {
        foreach (self::$routes as $pattern => $route) {
            if (preg_match("#{$pattern}#", $url, $matches)) {
                // если найдено соответствие
                foreach ($matches as $k => $v) {
                    if (is_string($k)) {
                        // создадим переменную и поместим в нее значения ключа
                        $route[$k] = $v;
                    }
                }
                if (empty($route['action'])) {
                    // если у нас нет Action
                    $route['action'] = 'index'; // Action по умолчанию index
                }
                if (!isset($route['prefix'])) {
                    $route['prefix'] = '';
                } else {
                    $route['prefix'] .= '\\';
                }
                $route['controller'] = self::upperCamelCase($route['controller']);
                self::$route = $route;
                return true;
            }
        }
        return false;
    }

    // метод приводит к формату CamelCase для имен Controller
    protected static function upperCamelCase($name) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $name)));
    }

    // метод приводит к формату camelCase для имен Action
    protected static function lowerCamelCase($name) {
        return lcfirst(self::upperCamelCase($name));
    }

    // метод для вырезания GET-параметров
    protected static function removeQueryString($url) {
        if ($url) {
            // если пареметр не пуст разбиваем на две части
            // неявные и явные GET-параметры
            $params = explode('&', $url, 2);
            // Проверяем первую часть
            if (false === strpos($params[0], '=')) {
                // если в ней отсутствует знак =
                // возвращаем эту часть без концевого слеша
                return rtrim($params[0], '/');
            } else {
                return '';
            }
        }
    }

}