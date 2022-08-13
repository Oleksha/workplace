<?php

use workplace\Router;

// пользовательские маршруты
Router::add('^partner/(?P<inn>[A-ZА-Я0-9]+)/?$', ['controller' => 'Partner', 'action' => 'view']);

// добавляем маршруты по умолчанию
Router::add('^admin$', ['controller' => 'Main', 'action' => 'index', 'prefix' => 'admin']);
Router::add('^admin/?(?P<controller>[a-z-]+)/?(?P<action>[a-z-]+)?$', ['prefix' => 'admin']);

Router::add('^$', ['controller' => 'Main', 'action' => 'index']);
Router::add('^(?P<controller>[a-z-]+)/?(?P<action>[a-z-]+)?$');