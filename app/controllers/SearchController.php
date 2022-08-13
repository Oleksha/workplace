<?php

namespace app\controllers;

use workplace\App;
use workplace\libs\Pagination;

class SearchController extends AppController {

    public function typeaheadAction() {
        if ($this->isAjax()) {
            $query = !empty(trim($_GET['query'])) ? trim($_GET['query']) : null;
            if ($query) {
                $partners = \R::getAll('SELECT id, name FROM partner WHERE name LIKE ? ORDER BY name LIMIT 11', ["%{$query}%"]);
                echo json_encode($partners);
            }
        }
        die;
    }

    public function indexAction() {


        $query = !empty(trim($_GET['s'])) ? trim($_GET['s']) : null;
        // получаем номер страницы
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        // получаем количество КА на одну страницу
        $perpage = App::$app->getProperty('pagination');
        // получаем количество КА
        $total = \R::count('partner', "name LIKE ?", ["%{$query}%"]);
        $pagination = new Pagination($page, $perpage, $total);
        $start = $pagination->getStart();
        if ($query) {
            $partners = \R::find('partner', "name LIKE ? LIMIT $start, $perpage", ["%{$query}%"]);
        }
        // Получение количества действующих ЕР
        foreach ($partners as $partner) {
            $ers = \R::getAll("SELECT * FROM er WHERE id_partner = ?", [$partner['id']]);
            $partner['er'] = count($ers);
        }
        // формируем метатеги для страницы
        $this->setMeta('Поиск по: ' . h($query), 'Описание...', 'Ключевые слова...');
        // Передаем полученные данные в вид
        $this->set(compact('partners','ers',  'query', 'pagination'));
    }

}