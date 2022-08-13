<?php

namespace app\controllers;

use app\models\Budget;
use app\models\Er;
use app\models\Payment;
use app\models\Receipt;
use app\models\Partner;
use RedBeanPHP\R;

class ReceiptController extends AppController {

    /**
     * Функция редактирования данных о приходе
     * @return void
     */
    public function editAction() {
        // создаем необходимые объекты связи с БД
        $receipt_obj = new Receipt(); // для прихода
        $ka_obj = new Partner();      // для контрагента        
        // получаем переданный идентификатор прихода
        $id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
        $receipt = null;
        if ($id) {
            // если у нас есть ID получаем все данные об этом приходе
            $receipt = $receipt_obj->getReceipt('id', $id);
            $receipt = $receipt[0];
            if (!$receipt) die; // если такого прихода нет дальнейшие действия бессмысленны
            // получаем все данные о КА
            $partner = $ka_obj->getPartnerByID($receipt['id_partner']);
        }
        if ($this->isAjax()) {
            // Если запрос пришел АЯКСом
            $this->loadView('receipt_edit_modal', compact('receipt', 'partner'));
        }
        redirect();
    }

    /**
     * Функция изменения данных о приходе в БД после редактирования
     * @return void
     */
    public function editReceiptAction() {
        // получаем данные пришедшие методом POST
        $edit_receipt = !empty($_POST) ? $_POST : null;
        $receipt = new Receipt();
        $receipt->load($edit_receipt);
        $receipt->edit('receipt', $edit_receipt['id']);
        redirect();
    }

    /**
     * Функция добавления нового прихода
     * @return void
     */
    public function addAction() {
        // получаем данные пришедшие методом GET
        $id = !empty($_GET['id']) ? $_GET['id'] : null; // Идентификатор КА
        $ka_obj = new Partner();
        $partner = $ka_obj->getPartnerByID($id);
        if ($this->isAjax()) {
            // Если запрос пришел АЯКСом
            $this->loadView('receipt_add_modal', compact('partner'));
        }
        redirect();
    }

    /**
     * Функция добавления данных о новом приходе в БД
     * @return void
     */
    public function addReceiptAction() {
        // получаем данные пришедшие методом POST
        $add_receipt = !empty($_POST) ? $_POST : null;
        $receipt = new Receipt();
        $receipt->load($add_receipt);
        $receipt->save('receipt');
        redirect();
    }

    /**
     * Функция удаления выбранного прихода
     * @return void
     */
    public function delAction() {
        // получаем переданный идентификатор прихода
        $id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
        if ($id) {
            R::hunt('receipt', 'id = ?', [$id]);
        }
        redirect();
    }

    /**
     * Функция Добавления или редактирования ЗО прихода
     * @return void
     */
    public function payAction() {
        // создаем необходимые объекты связи с БД
        $receipt_obj = new Receipt(); // для КА
        $er_obj = new Er();           // для ЕР
        $payment_obj = new Payment(); // для ЗО
        // получаем переданные GET данные
        $id = !empty($_GET['id']) ? (int)$_GET['id'] : null;                    // идентификатор прихода  
        $vat = !empty($_GET['vat']) ? $_GET['vat'] : null;                      // ставка НДС по которой работает КА
        $partner_id = !empty($_GET['partner']) ? (int)$_GET['partner'] : null;  // идентификатор контрагента
        $receipt = $receipt_obj->getReceipt('id', $id);                    // получаем полные данные о текущем приходе
        $receipt = $receipt[0];
        $pay_key = !is_null($receipt['date_pay']);                              // индикатор оплаты прихода
        /***** Начало получения данных для формирования заявки на оплату (ЗО) ******/
        /* Получаем все действующие ЕР для этого КА на момент прихода */
        $ers = $er_obj->getCurrentErFromDate($partner_id, $receipt['date']);
        $er = [];
        foreach ($ers as $k => $v) {
            $er[$k]['budget'] = $v['name_budget_item'];
            $er[$k]['number'] = $v['number'];
        }
        $ers = $er;
        /* Проверяем есть ли у этого прихода завка на оплату (ЗО) */
        $name = $receipt['partner'];                                 // Имя КА (ВСС ООО)
        $year = date('Y', strtotime($receipt['date']));       // Получаем год прихода (2022)
        $receipt_num = '%' . $receipt['number'] . '/' . $year . '%'; // Получаем используемый номер прихода (TOF00000000/2022)
        //$payments = \R::findOne('payment', "receipt LIKE ?", [$receipt_num]);   // Получаем заявку на оплату для этого прихода (если есть)
        $payments = $payment_obj->getPaymentFromReceipt($receipt_num);  // Получаем заявку на оплату для этого прихода (если есть)
        if ($payments) $payments = $payments[0];
        //$receipts = \R::find('receipt', 'partner = ? AND date_pay IS NULL ORDER BY date', [$name]); // Получаем все неоплаченные приходы этого КА
        $receipts = $receipt_obj->getReceiptNoPay($name); // Получаем все неоплаченные приходы этого КА
        /***************** Получаем массив приходов в зависимости от режима
        Array (пример)
        (
            [0] => Array
                (
                    [number] => TOF00000278/2022 - номер неоплаченного прихода
                    [summa] => 37044.00          - сумма этого прихода
                

            [1] => Array
                (
                    [number] => TOF00000279/2022
                    [summa] => 20752.88
                )

            [2] => Array
                (
                    [number] => TOF00000280/2022
                    [summa] => 3998.74
                )   
        ) *****************************************************************/
        $receipt_select = []; // массив содержащий выбранные приходы в ЗО
        $receipt_no_pay = [];  // массив содержащий неоплаченные приходы данного КА
        $ers_sel = []; $new_er = []; $new_sums = []; $new_recs = [];
        foreach ($receipts as $k => $v) {
            $receipt_no_pay[$k]['number'] = dateYear($v['number'], $v['date']);
            $receipt_no_pay[$k]['summa'] = $v['sum'];
        }
        if (!$payments) {
            // Если ЗО нет (режим добавления)
            $receipt_select['0']['number'] = trim($receipt_num, '%');
            $receipt_select['0']['summa'] = $receipt['sum'];
        } else {              
            $er_sel = explode(';', $payments['num_er']); // выбранные ер
            $er_sum = explode(';', $payments['sum_er']); // суммы выбранных ер
            foreach ($er_sel as  $k => $v) {
                $new_er[$k]['number'] = $v;
                $new_er[$k]['summa'] = $er_sum[$k];
            }
            $ers_sel = $new_er;
            $recs = explode(';', $payments['receipt']); // доступные приходы
            $sums = explode(';', $payments['sum']); // все выбранные приходы
            foreach ($recs as  $k => $v) {
                $new_recs[$k]['number'] = $v;
                $new_recs[$k]['summa'] = $sums[$k];
            }
            $receipt_select = $new_recs;
            if ($pay_key) {
                // Если ЗО создана и уже оплачена (режим просмотра)
                $receipt_no_pay = $new_recs;
            } else {
                // Если ЗО создана но пока не оплачена (режим редактирования)
                foreach ($receipts as $k => $v) {
                    $new_sums[$k]['number'] = dateYear($v['number'], $v['date']);
                    $new_sums[$k]['summa'] = $v['sum'];
                }
                $receipt_no_pay = $new_sums;
            }
        }
        /***** Конец получения данных для формирования заявки на оплату ******/
        //debug($ers);die;
        if ($this->isAjax()) {
            // Если запрос пришел АЯКСом
            $this->loadView('payment_add_modal', compact('name', 'receipt_select', 'receipt_no_pay', 'ers', 'ers_sel', 'payments', 'vat'));
        }
        redirect();
    }

    /**
     * Функция строкового представления массива
     * @param $data array входной массив
     * @return string строка значений массива разделенных символом ;
     */
    public function prepareData($data) {
        $num = '';                        // обнуляем переменную
        foreach ($data as &$value) {
            $num .= $value . ';';         // добавляем значение массива с символом ; в конце
        }
        return rtrim($num, ';'); // возвращаем строку без конечного знака ;
    }

    /**
     * проверка правильности заполнения полей формы
     * @param $data
     * @return bool
     */
    protected function checkPay($data) {
        $verify = true;
        if (!$this->checkNumBO($data['num_bo'], substr($data['date'], 0, 4))) {
            $_SESSION['error_payment'][] = 'Ошибка заполнения поля НОМЕР БО';
            $verify = false;
        }
        if (count($data['receipt']) != count($data['sum'])) {
            $_SESSION['error_payment'][] = 'Не совпадает количество выбранных приходов и сумм';
            $verify =  false;
        }
        if (count($data['num_er']) != count(explode(';', $data['sum_er']))) {
            $_SESSION['error_payment'][] = 'Не совпадает количество выбранных ЕР и введенных сумм';
            $verify =  false;
        }
        if (count(explode(';', $data['num_bo'])) != count(explode(';', $data['sum_bo']))) {
            $_SESSION['error_payment'][] = 'Не совпадает количество введенных БО и введенных сумм';
            $verify =  false;
        }
        $a = array_sum($data['sum']);
        $b = array_sum(explode(';', $data['sum_er']));
        $epsilon = 0.00001;
        if (abs($a - $b) < $epsilon) {
            //echo "true";
        } else {
            $_SESSION['error_payment'][] = "Не совпадает сумма выбранных приходов {$a} и суммы ЕР {$b}";
            $verify =  false;
        }
        $b = array_sum(explode(';', $data['sum_bo']));
        if (abs($a - $b) < $epsilon) {
            //echo "true";
        } else {
            $_SESSION['error_payment'][] = "Не совпадает сумма выбранных приходов {$a} и суммы БО {$b}";
            $verify =  false;
        }
        if (count(explode(';', $data['sum_er'])) == count($data['num_er'])) {
            $er_obj = new Er();
            $ers = $data['num_er'];
            $sums = explode(';', $data['sum_er']);
            foreach ($ers as $k => $v) {
                $sum = $sums[$k];
                $sum = round($sum / $data['vat'], 2);
                // получаем текущие данные
                $current['number'] = $data['number'] . '/' . substr($data['date'], 0, 4);
                $current['summa'] = $sum;
                // получаем остаток средств на ЕР
                $coast = $er_obj->getBalance($v, $data['id_partner']);
                //$pays_arr = $er_obj->getPaymentCoast($v);
                //$er = $er_obj->getEr($v);
                //$summa = $er['summa'];
                //$total = 0.00;
                //foreach ($pays_arr as $item) {
                //    if (($item['summa'] != $current['summa']) && ($item['number'] != $current['number'])) {
                //        $total += $item['summa'];
                //    }
                //}
                //$coast = $summa - $total;
                if (abs($sum - $coast) > $epsilon) {
                    if ($sum > $coast) {
                        $_SESSION['error_payment'][] = "Не хватает средств. Требуется сумма {$sum}, а в ЕР ({$v}) осталось {$coast}";
                        $verify =  false;
                    }
                }
            }
        } else {
            $a = count($data['num_er']);
            $b = count(explode(';', $data['sum_er']));
            $_SESSION['error_payment'][] = "Не совпадает сумма количество введеных ЕР {$a} и количество сумм ЕР {$b}";
            $verify =  false;
        }
        if (count(explode(';', $data['sum_bo'])) == count(explode(';', $data['num_bo']))) {
            $bo_obj = new Budget();
            $bos = explode(';', $data['num_bo']);
            $sums = explode(';', $data['sum_bo']);
            foreach ($bos as $k => $v) {
                $sum = $sums[$k];

                // получаем все оплаты по этой <БО>
                $pays_arr = $bo_obj->getPaymentCoast($v);
                $bo = $bo_obj->getBo($v);
                // получаем текущие данные
                $current['number'] = $data['number'] . '/' . substr($data['date'], 0, 4);
                if ($bo['vat'] = '1.20') {
                    if ($data['vat'] = '1.20') {
                        $current['summa'] = $sum;
                    }
                    if ($data['vat'] = '1.00') {
                        $current['summa'] = $sum * 1.2;
                    }
                }
                if ($bo['vat'] = '1.00') {
                    if ($data['vat'] = '1.00') {
                        $current['summa'] = $sum;
                    }
                    if ($data['vat'] = '1.20') {
                        $current['summa'] = $sum / 1.2;
                    }
                }
                $summa = $bo['summa'];
                $total = 0.00;
                foreach ($pays_arr as $item) {
                    if (($item['summa'] != $current['summa']) && ($item['number'] != $current['number'])) {
                        $total += $item['summa'];
                    }
                }
                $coast = $summa - $total; // оставшаяся сумма БО
                if (abs($sum - $coast) > $epsilon) {
                    if ($sum > $coast) {
                        $_SESSION['error_payment'][] = "Не хватает средств. Требуется сумма {$sum}, а в БО ({$v}) осталось {$coast}";
                        $verify =  false;
                    }
                }
            }
        } else {
            $a = count(explode(';', $data['num_bo']));
            $b = count(explode(';', $data['sum_bo']));
            $_SESSION['error_payment'][] = "Не совпадает количество введеных БО {$a} и количество сумм БО {$b}";
            $verify =  false;
        }
        return $verify;
    }

    /**
     * проверка правильности заполнения поля НОМЕРА БО
     * @param $data string содержимое поля
     * @param $year string год ЗО
     * @return bool результат проверки
     */
    protected function checkNumBO($data, $year) {
        // получаем массив номера заполненных БО
        $bos = explode(';', $data);
        // просматриваем каждую строку массива
        foreach ($bos as $bo) {
            if (strlen($bo) != 18) {
                // проверка длинны каждой БО
                return false;
            } else {
                // проверка соответствия БО шаблону CUB0123456789/2022
                preg_match('/CUB[0-9]{10}\/[0-9]{4}/', $bo, $matches);
                if (empty($matches)) {
                    return false;
                } else {
                    // проверяем правильность заполнения года
                    $str = explode('/', $bo);
                    $year_bo = (int)$str[1];
                    $years = [(int)$year - 1, (int)$year, (int)$year + 1];
                    if (!in_array($year_bo, $years)) return false;
                }
            }
        }
        return true;
    }

    /**
     * Функция записывающая в БД ЗО и вносящая исправления в приходы оплаченные этой ЗО
     * @return void
     */
    public function payReceiptAction() {
        // получаем данные пришедшие методом POST
        $pay_receipt = !empty($_POST) ? $_POST : null;
        // проверяем полученные данные
        if (!$this->checkPay($pay_receipt)) {
            // запоминаем значения формы
            $_SESSION['form_data'] = $pay_receipt;
            //debug($_SESSION['form_data']);die;
            redirect();
        }
        $receipts = $this->getReceipts($pay_receipt['receipt']); // получаем массив ID приходов
        // исправляем данные пришедшие в виде массива        
        $pay_receipt['num_er'] = $this->prepareData($pay_receipt['num_er']);
        $pay_receipt['sum'] = $this->prepareData($pay_receipt['sum']);
        $pay_receipt['receipt'] = $this->prepareData($pay_receipt['receipt']);
        if (empty($pay_receipt['date_pay'])) $pay_receipt['date_pay'] = null;
        // внесение изменений в ЗО
        $pay = new Payment();
        $pay->load($pay_receipt);
        if (empty($pay_receipt['id'])) {
            // это новая ЗО
            $pay->save('payment'); 
        } else {
            // это редактируемая ЗО
            $pay->edit('payment', $pay_receipt['id']);            
        }
        // внесение изменений в приходы
        foreach ($receipts as $value) {
            $edit_receipt['id'] = $value['id'];
            $edit_receipt['date'] = $value['date'];
            $edit_receipt['number'] = $value['number'];
            $edit_receipt['sum'] = $value['sum'];
            $edit_receipt['type'] = $value['type'];
            $edit_receipt['vat'] = $value['vat'];
            $edit_receipt['partner_name'] = $value['partner'];
            $edit_receipt['id_partner'] = $value['id_partner'];
            $edit_receipt['num_doc'] = $value['num_doc'];
            $edit_receipt['date_doc'] = $value['date_doc'];
            $edit_receipt['note'] = $value['note'];
            $edit_receipt['num_pay'] = dateYear($pay_receipt['number'], $pay_receipt['date']);
            $edit_receipt['date_pay'] = $value['date_pay'];
            $receipt = new Receipt();
            $receipt->load($edit_receipt);
            $receipt->edit('receipt', $edit_receipt['id']);
        }
        unset($_SESSION['form_data']);
        redirect("/partner/{$pay_receipt['inn']}");
    }

    /**
     * Функция возвращающая массив полных данных по приходам
     * @param $receipt array Строка составных номеров приходов оплаченных данно ЗО
     * @return array Полные данные о приходах
     */
    public function getReceipts($receipt) {
        $receipts = []; // объявляем массив
        // проходимся по всем элементам массива
        foreach ($receipt as &$value) {
            $num_receipt = explode('/', $value); // получаем составной номер прихода
            $number = $num_receipt[0]; // выделяем номер
            $year = $num_receipt[1];   // выделяем год
            // получаем полные данные о приходе
            $receipt_full = \R::findOne('receipt', "number = ? AND YEAR(date) = {$year}", [$number]);
            $receipts[] = $receipt_full;
        }
        return $receipts;
    }

}
