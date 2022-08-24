<?php

namespace app\controllers;

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
            $payment['num_er_id'] = $ers_id;
            /****** добавляем идентификаторы ЕР ******/

            /****** добавляем идентификаторы БО ******/
            $bos_id = '';
            $bos = explode(';', $payment['num_bo']); 
            foreach ($bos as $bo) {
                if ($this->getBO($bo) == false) { echo 'отсутсвует БО '.$bo. ' в оплате '.$payment['number']. ' - '.$payment['date']; return; }
                $bos_id .= $this->getBO($bo) . ';';
            }
            $bos_id = rtrim($bos_id, ';');
            $payment['num_bo_id'] = $bos_id;
            /****** добавляем идентификаторы БО ******/
            debug($payment);
        }     
        //debug($payments);   
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

}
