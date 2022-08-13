<?php

namespace app\controllers;

use app\models\AppModel;
use workplace\base\Controller;

/**
 * Class AppController
 * @package app\controllers
 * Контроллер этого приложения
 */
class AppController extends Controller {

    public function __construct($route)
    {
        parent::__construct($route);
        new AppModel();
    }

}