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

function dateYear($data, $date) {
    $year = date('Y', strtotime($date));
    return $data . '/' . $year;
}