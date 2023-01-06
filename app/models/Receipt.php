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
        'pay_id' => null,
        'date_pay' => null,
    ];

    /**
     * Возвращает массив неоплаченных приходов для КА
     * @param $id int идентификатор КА
     * @return array|false
     */
    public function getReceiptNoPay(int $id) {
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
    public function getReceipt(string $field, string $value) {
        $receipts = \R::getAssocRow("SELECT * FROM receipt WHERE $field = ? ORDER BY date", [$value]);
        if (!empty($receipts)) return $receipts;
        return false;
    }

    /**
     * Возвращает текущий тип поступления 
     * (1 - просмотр - для уже оплаченных поступлений)
     * (2 - редактор - поданные на оплату но еще не плаченные поступления)
     * (3 - оплата - не поданные на оплату (по умолчанию))
     * @param $id int номер поступления товаров или услуг
     * @return int
     */
    public function isTypeReceipt(int $id): int  {
        $isType = 3;
        $receipt = $this->getReceipt('id', $id);
        if ($receipt) {
            $receipt = $receipt[0];
            if (!empty($receipt['date_pay'])) {
                $isType = 1;
            } elseif (!empty($receipt['pay_id'])) {
                $isType = 2;
            }
        }
        return $isType;
    }

    /**
     * Функция возвращающая массив полных данных по приходам
     * @param $id array Строка ID приходов оплачиваемых данно ЗО
     * @return array Полные данные о приходах
     */
    public function getReceipts(array $id): array {
        $receipts = []; // объявляем массив
        // проходимся по всем элементам массива
        //$receipt = explode(';', $id);
        foreach ($id as $item) {
            $receipt_full = \R::findOne('receipt', "id = ?", [$item]);
            $receipts[] = $receipt_full;
        }
        return $receipts;
    }

    public function getReceiptMain(): array {
        $receipts = \R::getAssocRow("SELECT * FROM receipt WHERE (date_pay is NULL) OR (date_pay = CURDATE())");
        if (!empty($receipts)) return $receipts;
        return false;
    }

}
