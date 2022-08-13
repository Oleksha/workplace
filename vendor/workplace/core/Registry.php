<?php

namespace workplace;

class Registry {

    use TSingletone;

    protected static $properties = []; // содержит все свойства

    // метод который добавляет в массив свойство с ключем и значением
    public function setProperty($name, $value) {
        self::$properties[$name] = $value;
    }

    // метод который получает значение имеющегося свойства по ключу
    public function getProperty($name) {
        if (isset(self::$properties[$name])) {
            // если свойство существует возвращаем его
            return self::$properties[$name];
        }
        return null; // иначе ничего не возвращаем
    }

    /**
     * @return array - возвращает массив со свойствами
     */
    public function getProperties() {
        return self::$properties;
    }

}