<?php

namespace app\controllers;

use RedBeanPHP\R;

class ConvertController extends AppController {

    public function indexAction() {
        
        $this->updateTable();die;

        // формируем метатеги для страницы
        $this->setMeta('Подготовка данных');

    }

    public function updateTable() {
        $payments = \R::getAssocRow("SELECT * FROM payment"); // получаем все оплаты из базы
        foreach ($payments as $payment) {
            /****** добавляем идентификаторы приходов ******/
            $receipts_id = '';
            $receipts = explode(';', $payment['receipt']);
            foreach ($receipts as $receipt) {
                if ($this->getReceipt($receipt) == false) { echo 'отсутсвует поступление '.$receipt; return; }
                $receipts_id .= $this->getReceipt($receipt) . ';';
            }
            $receipts_id = rtrim($receipts_id, ';');
            $payment['receipts_id'] = $receipts_id;
            /****** добавляем идентификаторы приходов ******/

            /****** добавляем идентификаторы ЕР ******/
            $ers_id = '';
            $ers = explode(';', $payment['num_er']);
            foreach ($ers as $er) {
                if ($this->getER($er, $payment['id_partner']) == false) { echo 'отсутсвует ЕР '.$er. ' в оплате '.$payment['id_partner']; return; }
                $ers_id .= $this->getER($er, $payment['id_partner']) . ';';
            }
            $ers_id = rtrim($ers_id, ';');
            $payment['ers_id'] = $ers_id;
            /****** добавляем идентификаторы ЕР ******/

            /****** добавляем идентификаторы БО ******/
            $bos_id = '';
            $bos = explode(';', $payment['num_bo']);
            foreach ($bos as $bo) {
                if ($this->getBO($bo) == false) { echo 'отсутсвует БО '.$bo. ' в оплате '.$payment['number']. ' - '.$payment['date']; return; }
                $bos_id .= $this->getBO($bo) . ';';
            }
            $bos_id = rtrim($bos_id, ';');
            $payment['bos_id'] = $bos_id;
            /****** добавляем идентификаторы БО ******/
            /* Обновление таблицы начало*/
            // создаем массив с полями таблицы
            /*$pay = [];
            $pay['date'] = $payment['date'];
            $pay['number'] = $payment['number'];
            $pay['sum'] = $payment['sum'];
            $pay['receipts_id'] = $payment['receipts_id'];
            $pay['vat'] = $payment['vat'];
            $pay['partner_id'] = $payment['id_partner'];
            $pay['ers_id'] = $payment['num_er_id'];
            $pay['ers_sum'] = $payment['sum_er'];
            $pay['budgets_id'] = $payment['num_bo_id'];
            $pay['budgets_sum'] = $payment['sum_bo'];
            $pay['pay_date'] = $payment['date_pay'];*/
            //debug($payment);
            $tbl = R::load('payment', $payment['id']); // подключаем источник данных payments
            //debug($tbl);
            /*$tbl->date = $payment['date'];
            $tbl->number = $payment['number'];
            $tbl->sum = $payment['sum'];
            $tbl->receipt = $payment['receipt'];
            $tbl->vat = $payment['vat'];
            $tbl->id_partner = $payment['id_partner'];
            $tbl->num_er = $payment['num_er'];
            $tbl->sum_er = $payment['sum_er'];
            $tbl->num_bo = $payment['num_bo'];
            $tbl->sum_bo = $payment['sum_bo'];
            $tbl->date_pay = $payment['date_pay'];*/
            $tbl['receipts_id'] = $payment['receipts_id'];
            $tbl['ers_id'] = $payment['ers_id'];
            $tbl['bos_id'] = $payment['bos_id'];
            /*foreach ($payment as $name => $value) {
                // проходим по всем атрибутам содержащим данные для добавления
                $tbl[$name] = $value;
            }
            //debug($tbl);die;*/
            R::store($tbl);
            /* Обновление таблицы конец*/

        }
        $receipts = \R::getAssocRow("SELECT * FROM receipt"); // получаем все приходы из базы
        foreach ($receipts as $receipt) {
            /****** добавляем идентификаторы оплат ******/
            $paiment_id = '';
            if (!empty($receipt['num_pay'])) {
                // если есть данные об оплате
                $paiment_id = $this->getPayment($receipt['num_pay']);
            }
            $receipt['pay_id'] = $paiment_id;
            /****** добавляем идентификаторы оплат ******/
            $tbl = R::load('receipt', $receipt['id']); // подключаем источник данных payments
            $tbl['pay_id'] = $receipt['pay_id'];
            R::store($tbl);
            //debug($receipt);
        }
        //debug($receipts);   
    }

    public function getReceipt($numbers) {
        //debug($numbers);
        $numbers = explode('/', $numbers);
        $number = $numbers[0];
        $year = (int)$numbers[1];
        //debug($number);debug($year);
        $receipt = \R::getAssocRow("SELECT * FROM receipt WHERE YEAR(date) = {$year} AND number = '{$number}'");
        //debug($receipt);
        if (!empty($receipt)) { $receipt = $receipt[0]; return $receipt['id']; }
        return false;
    }

    public function getER($number, $id_partner) {
        //debug($number);debug($id_partner);
        $er = \R::getAssocRow("SELECT * FROM er WHERE id_partner = {$id_partner} AND number = '{$number}'");
        //debug($er);        
        //debug($receipt);
        if (!empty($er)) { $er = $er[0]; return $er['id']; }
        return false;
    }
    
    public function getBO($numbers) {
        //debug($numbers);
        $numbers = explode('/', $numbers);
        if (!isset($numbers[1])) return false;
        $number = $numbers[0];
        $year = (int)$numbers[1];
        //debug($number);debug($year);
        $receipt = \R::getAssocRow("SELECT * FROM budget WHERE YEAR(scenario) = {$year} AND number = '{$number}'");
        //debug($receipt);
        if (!empty($receipt)) { $receipt = $receipt[0]; return $receipt['id']; }
        return false;
    }
    
    public function getPayment($numbers) {
        //debug($numbers);
        $numbers = explode('/', $numbers);
        if (!isset($numbers[1])) return false;
        $number = $numbers[0];
        $year = (int)$numbers[1];
        //debug($number);debug($year);
        $payment = \R::getAssocRow("SELECT * FROM payment WHERE YEAR(date) = {$year} AND number = '{$number}'");
        //debug($receipt);
        if (!empty($payment)) { $payment = $payment[0]; return $payment['id']; }
        return false;
    }

}
