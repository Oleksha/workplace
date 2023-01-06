<?php

function debug($arr) {
    echo '<pre>' . print_r($arr, true) . '</pre>';
}

function redirect($http = false) {
    if ($http) {
        $redirect = $http;
    } else {
        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : PATH;
    }
    header("Location: $redirect");
    exit;
}

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES);
}

/**
 * ¬озвращает разницу из элементов массива
 * @param $arr1 array массив содержащий большее число элементов
 * @param $arr2 array массив содержащий часть элементов первого массива
 * @return array 
 */
function my_array_diff(&$arr1, &$arr2): array {
    $diff = [];
    if(is_array($arr1) and is_array($arr2)) {
        foreach ($arr1 as $item) {
            $key = false;
            foreach ($arr2 as $value) {
                if ($item['number'] === $value['number']) {
                    $key = true;
                    break;
                }
            }
            if (!$key) {
                $diff[] = $item;
            }
        }
    }
    return $diff;
}

function dateYear($data, $date) {
    $year = date('Y', strtotime($date));
    return $data . '/' . $year;
}