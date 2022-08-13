<?php

namespace workplace;

class Cache {

    use TSingletone;

    /**
     * @param $key - уникальный ключ
     * @param $data - кешируемые данные
     * @param int $seconds - время хранения данных
     */
    public function set($key, $data, $seconds = 3600) {
        if ($seconds) {
            // если мы хотим кэшировать данные
            $content['data'] = $data; // помещаем кешированные данные в переменную
            $content['end_time'] = time() + $seconds; // устанавливаем время хранения
            if (file_put_contents(CACHE . '/' . md5($key) . '.txt', serialize($content))) return true;
        }
        return false;
    }

    public function get($key) {
        $file = CACHE . '/' . md5($key) . '.txt'; // формируем путь к файлу
        if (file_exists($file)) {
            // если файл существует получаем из него данные
            $content = unserialize(file_get_contents($file));
            // проверяем не устарели ли данные
            if (time() <= $content['end_time']) {
                return $content;
            }
            // если данных нет или кеш устарел
            unlink($file); // удаляем файл
        }
        return false;
    }

    public function delete($key) {
        $file = CACHE . '/' . md5($key) . '.txt'; // формируем путь к файлу
        if (file_exists($file)) {
            // если файл существует
            unlink($file); // удаляем файл
        }
    }

}