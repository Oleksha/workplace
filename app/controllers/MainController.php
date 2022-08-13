<?php

namespace app\controllers;

use app\models\Partner;
use app\models\Receipt;

class MainController extends AppController {

    public function indexAction() {
        // получение ассоциативный массив не оплаченных приходов из БД
        $receipts = \R::getAssocRow("SELECT * FROM receipt WHERE (date_pay is NULL) OR (date_pay = CURDATE())");
        // Получаем дополнительную информацию для каждого прихода
        foreach ($receipts as $k => $v) {
            // Получаем всю информацию о КА
            $partners = new Partner();
            $partner = $partners->getPartnerByID($v['id_partner']);
            if ($partner) {
                // если КА существует дописываем ИНН и наименование КА
                $receipts[$k]['inn'] = $partner['inn'];
                $receipts[$k]['partner'] = $partner['name'];
                // дата планируемой оплаты
                $receipts[$k]['pay_date'] = $this->getDatePayment(dateYear($v['number'], $v['date']));
                // задержка
                $receipts[$k]['delay'] = isset($partner['delay']) ? $partner['delay'] : null;
            }
        }
        // формируем метатеги для страницы
        $this->setMeta('Главная страница', 'Содержит информацию о неоплаченных приходах', 'Ключевые слова');
        // Передаем полученные данные в вид
        $this->set(compact('receipts'));
    }

    /**
     * Функция обработки нажатия кнопки Ввод оплаты
     * @return void
     */
    public function payAction() {
        // получаем переданный идентификатор прихода
        $id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
        if ($this->isAjax()) {
            // Если запрос пришел АЯКСом
            $this->loadView('pay_add_date', compact('id'));
        }
        redirect();
    }

    /**
     * Функция занесения в БД данных об оплате прихода
     * @return void
     */
    public function payEnterAction() {
        // получаем данные пришедшие методом POST
        $data = !empty($_POST) ? $_POST : null;
        $id_receipt = $data['id'];
        // получаем приход в который необходимо внести дату оплаты
        $edit_receipt = \R::findOne('receipt', 'id = ?', [$id_receipt]);
        $edit_receipt['date_pay'] = $data['date']; // заносим оплату
        // записываем исправленные данные в БД
        $receipt = new Receipt();
        /** @var array $edit_receipt */
        $receipt->load($edit_receipt);
        $receipt->edit('receipt', $id_receipt);
        redirect();
    }

    /**
     * Функция получения данных об оплате конкретного прихода
     * @param $num_receipt mixed номер прихода в виде 0000000000/2022 или массив номеров
     * @return mixed
     */
    public function getDatePayment($num_receipt) {
        $date_payment = null;
        // получаем данные об оплатах данного прихода
        $receipts = \R::getAll("SELECT * FROM payment WHERE receipt LIKE ?", ['%'.$num_receipt.'%']);
        foreach ($receipts as $receipt) { 
            if (!is_null($date_payment)) {
                if ($receipt['date_pay'] > $date_payment) {
                    $date_payment = $receipt['date_pay'];
                }
            } else {
                $date_payment = $receipt['date_pay'];
            }
        }
        return $date_payment;
    }

}
