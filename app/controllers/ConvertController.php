<?php

namespace app\controllers;

class ConvertController extends AppController {

    public function indexAction() {
        
        //$this->updateTable();die;
        
        // формируем метатеги для страницы
        $this->setMeta('Подготовка данных');

    }
    
    public function updateTable() {
        $payments = \R::getAssocRow("SELECT * FROM payment");
        foreach ($payments as $payment) {
            $receipts_id = '';
            $receipts = explode(';', $payment['receipt']);         
            foreach ($receipts as $receipt) {
                //debug($receipt);
                $receipts_id .= $this->getReceipt($receipt) . ';';
            }
            $receipts_id = rtrim($receipts_id, ';');
            $payment['receipts_id'] = $receipts_id;
            $ers_id = '';
            $ers = explode(';', $payment['num_er']); 
            foreach ($ers as $er) {
                //debug($receipt);
                $ers_id .= $this->getER($er, $payment['id_partner']) . ';';
            }
            $ers_id = rtrim($ers_id, ';');
            $payment['num_er_id'] = $ers_id;
            debug($payment);
        }        
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

}
