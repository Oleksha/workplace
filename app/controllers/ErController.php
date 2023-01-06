<?php

namespace app\controllers;

use app\models\Er;
use app\models\Partner;
use app\models\Payment;
use RedBeanPHP\R;

class ErController extends AppController {

    /*public $id = null, // ID едииноличного решения
           $id_partner = null, // ID контрагента
           $er = [], // массив данных об ЕР
           $partner = null, // массив данных о контрагенте
           $budget = null; // массив данные о статьях расхода*/

    public function editAction() {
        // создаем необходимые объекты связи с БД
        $partner_models = new Partner(); // Контрагенты
        $er_models = new Er();           // Единоличные решения
        // получаем переданный идентификатор ЕР
        $id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
        if ($id) {
            // если у нас есть ID получаем все данные об этом ЕР
            $er = $er_models->getEr((int)$id);
            if (!$er) return false; // если такой нет дальнейшие действия бессмысленны
            // получаем данные о контрагенте
            $partner = $partner_models->getPartner($er['id_partner']);
            // получаем данные о всех статьях расходов для поля со списком
            $budget = \R::getAll("SELECT * FROM budget_items ORDER BY name_budget_item");
            if ($this->isAjax()) {
                // Если запрос пришел АЯКСом
                $this->loadView('er_edit_modal', compact('er', 'partner', 'budget'));
            }
        }
        redirect();
    }

    public function editErAction() {
        // создаем необходимые объекты связи с БД
        $er_models = new Er();           // Единоличные решения
        // получаем данные пришедшие методом POST
        $data = !empty($_POST) ? $_POST : null;
        $er_models->load($data);
        $er_models->edit('er', $data['id']);
        redirect();
    }

    public function addAction() {
        // создаем необходимые объекты связи с БД
        $partner_models = new Partner(); // Контрагенты
        // получаем переданный идентификатор КА
        $id_partner = !empty($_GET['id']) ? (int)$_GET['id'] : null;
        // получаем данные о контрагенте
        $partner = $partner_models->getPartner($id_partner);
        $budget = \R::getAll('SELECT * FROM budget_items ORDER BY name_budget_item');
        if ($this->isAjax()) {
            // Если запрос пришел АЯКСом
            $this->loadView('er_add_modal', compact('partner', 'budget'));
        }
        redirect();
    }

    public function addErAction() {
        // создаем необходимые объекты связи с БД
        $er_models = new Er();           // Единоличные решения
        // получаем данные пришедшие методом POST
        $data = !empty($_POST) ? $_POST : null;
        $er_models->load($data);
        $er_models->save('er');
        redirect();
    }

    public function delAction() {
        // получаем переданный идентификатор ЕР
        $id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
        if ($id) R::hunt('er', 'id = ?', [$id]);
        redirect();
    }

    public function viewAction() {
        // создаем необходимые объекты связи с БД
        $payment_models = new Payment(); // Оплаты
        $er_models = new Er();           // Единоличные решения
        // получаем переданный идентификатор ЕР
        $id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
        if ($id) {
            // если у нас есть номер получаем все данные об этом ЕР
            $er = $er_models->getEr($id);
            if (!$er) return false; // если такой нет дальнейшие действия бессмысленны
            // если у нас есть ЕР получаем данные об оплатах использующих это ЕР
            $payments = $payment_models->getPaymentEr($id);
            if ($this->isAjax()) {
                // Если запрос пришел АЯКСом
                $this->loadView('er_view_modal', compact('payments', 'er'));
            }
            redirect();
        }
    }

}