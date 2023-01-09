<?php

namespace app\controllers;

use app\models\Partner;
use app\models\Payment;
use app\models\Receipt;

class MainController extends AppController {

    public function indexAction() {
        // массив не оплаченных приходов из БД
        $receipt_model = new Receipt(); // для приходов
        $receipts = $receipt_model->getReceiptMain();
        // Получаем дополнительную информацию для каждого прихода
        foreach ($receipts as $k => $v) {
            // Получаем всю информацию о КА
            $partners = new Partner();
            $partner = $partners->getPartner($v['id_partner']);
            if ($partner) {
                // если КА существует дописываем ИНН и наименование КА
                $receipts[$k]['partner_id'] = $partner['id'];
                $receipts[$k]['partner'] = $partner['name'];
                // дата планируемой оплаты
                $receipts[$k]['pay_date'] = $this->getDatePayment($v['id']);
                // задержка
                $receipts[$k]['delay'] = $partner['delay'] ?? null;
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
     * @param $payment_id string идентификатор прихода
     * @return mixed
     */
    public function getDatePayment(string $payment_id) {
        $date_payment = '';
        // получаем данные об оплате данного прихода
        $payment_model = new Payment(); // для оплат
        $payments = $payment_model->getPaymentAll();
        foreach ($payments as $payment) {
            if (in_array($payment_id, explode(';', $payment['receipts_id']))) {
                // найдена оплата
                $date_payment = $payment['date_pay'];
            }
        }
        return $date_payment;
    }

}
