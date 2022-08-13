<?php

namespace app\models;

class Receipt extends AppModel {

    // поля таблицы для заполнения
    public $attributes = [
        'date' => '',
        'number' => '',
        'sum' => '',
        'type' => '',
        'vat' => '',
        'id_partner' => 0,
        'num_doc' => '',
        'date_doc' => '',
        'note' => null,
        'num_pay' => null,
        'date_pay' => null,
    ];

    /**
     * Возвращает массив неоплаченных приходов для КА
     * @param $id string идентификатор КА
     * @return array|false
     */
    public function getReceiptNoPay($id) {
        $receipts = \R::getAssocRow('SELECT * FROM receipt WHERE id_partner = ? AND date_pay IS NULL ORDER BY date', [$id]);
        if (!empty($receipts)) return $receipts;
        return false;
    }

    /**
     * Возвращает массив всех приходов
     * @param $field string поле по которому происходит отбор
     * @param $value string значение по которому происходит отбор
     * @return array|false
     */
    public function getReceipt($field, $value) {
        $receipts = \R::getAssocRow("SELECT * FROM receipt WHERE $field = ? ORDER BY date", [$value]);
        if (!empty($receipts)) return $receipts;
        return false;
    }

    /**
     * Возвращает текущий тип поступления 
     * (1 - просмотр - для уже оплаченных поступлений)
     * (2 - редактор - поданные на оплату но еще не плаченные поступления)
     * (3 - оплата - не поданные на оплату (по умолчанию))
     * @param $number string номер поступления товаров или услуг
     * @return array|false
     */
    public function isTypeReceipt($number) {
        
    }

}
