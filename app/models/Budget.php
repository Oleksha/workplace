<?php

namespace app\models;

use R;

class Budget extends AppModel {

    // поля таблицы для заполнения
    public $attributes = [
        'scenario' => '',
        'month_exp' => '',
        'month_pay' => '',
        'number' => '',
        'summa' => '',
        'vat' => '',
        'budget_item_id' => 0,
        'status' => '',
    ];
    public array $payment = [
        'date' => '',
        'number' => '',
        'sum' => '',
        'vat' => '',
        'partner' => '',
        'id_partner' => 0,
        'receipt' => '',
        'num_er' => '',
        'sum_er' => '',
        'num_bo' => '',
        'sum_bo' => '',
        'date_pay' => '',
    ];

    /**
     * Возвращает массив оплат использующих БО
     * @param $number string номер БО в формате (CUB000000000/2021)
     * @return array
     */
    public function getBudgetPayment(string $number): array {
        $pays = []; $payments = [];
        $number = '%' . $number . '%';
        $pay_arrays = R::find('payment', 'num_bo LIKE ? ORDER BY date_pay', [$number]);
        foreach ($pay_arrays as $pay_array) {
            // проходим по всем атрибутам
            foreach ($this->payment as $name => $value) {
                // если в переданных данных data есть имя ключа атрибута
                if (isset($pay_array[$name])) {
                    // запоминаем в атрибуте соответсвующее значение
                    $pays[$name] = $pay_array[$name];
                    if ($name == 'partner') {
                        $pays[$name] = $this->getBudgetPartner($pay_array[$name]);
                    }
                }
            }
            $payments[] = $pays;
        }
        return $payments;
    }

    /**
     * Возвращает массив данных о КА
     * @param $names string наименование КА
     * @return array
     */
    public function getBudgetPartner(string $names): array
    {
        $partner = R::getAssocRow('SELECT * FROM partner WHERE name = ?', [$names]);
        return $partner[0];
    }

    /**
     * Функция возвращает диапазон +/- 1 месяц от переданной даты
     * @param $date string переданная дата
     * @return void
     */
    public function getRangeOneMonth(string $date) {

    }

    /**
     * Возвращает массив содержащий номера оплат и суммы расхода по БО
     * @param $num_bo string номер BO в формате CUB0000000000/2022
     * @return array
     */
    public function getPaymentCoast(string $num_bo): array {
        $pay_obj = new Payment();
        $bo = $this->getBo($num_bo);
        $bo['number'] = $bo['number'] . '/' . substr($bo['scenario'], 0, 4);
        $pay = [];
        // получаем все оплаты использующие эту БО
        $payments = $pay_obj->getPaymentBo($num_bo);
        if ($payments) {
            // проходимся по всем оплатам использующим нашу ЕР
            $st = 0.00;

            foreach ($payments as $payment) {
                $vat = $payment['vat'];
                $nums = explode(';', trim($payment['num_bo']));
                $sums = explode(';', trim($payment['sum_bo']));
                $key = array_search($bo['number'], $nums);
                $sum = $sums[$key];
                $pay_bo['number'] = $payment['number'] . '/' . substr($payment['date'], 0, 4);
                if ($bo['vat'] == '1.20') {
                    if ($vat == '1.20') {
                        $pay_bo['summa'] = $sum;
                    }
                    if ($vat == '1.00') {
                        $pay_bo['summa'] = round($sum * 1.2, 2);
                    }
                }
                if ($bo['vat'] == '1.00') {
                    if ($vat == '1.20') {
                        $pay_bo['summa'] = round($sum / 1.2, 2);
                    }
                    if ($vat == '1.00') {
                        $pay_bo['summa'] = $sum;
                    }
                }
                $st += $pay_bo['summa'];
                $pay[] = $pay_bo;
            }
        }
        
        return $pay;
    }


    /**
     * Возвращает данные БО по ее номеру
     * @param $num_bo string номер БО в формате CUB0000000000/2022
     * @return array
     */
    public function getBo(string $num_bo): array {
        $bo_arr = [];
        if (strpos($num_bo, '/')) {
            $bos = explode('/', $num_bo);
            $bo = R::getAssocRow("SELECT * FROM budget WHERE status = 'Согласован' AND YEAR(scenario) = ? AND number = ?", [$bos[1], $bos[0]]);
            $bo_arr = $bo[0];
        }
        return $bo_arr;
    }

}
