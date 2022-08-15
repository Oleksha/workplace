<?php

namespace app\controllers;

use app\models\Budget;
use app\models\Partner;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BudgetController extends AppController {

    public function indexAction() {
        // получаем сценарий для просмотра бюджетных операций если он есть
        $filter_date = $_GET['filter'] ?? date('Y-m-d');
        $year = mb_substr($filter_date, 0, 4);  // выделяем месяц сценария
        $month = mb_substr($filter_date, 5, 2); // выделяем год сценария
        $scenario = $year . '-' . $month . '-01';
        // получение данных из БД соответственно сценарию
        $budgets = \R::find('budget', "WHERE scenario = '{$scenario}' AND status = 'Согласован' ORDER BY scenario, number");
        
        // получаем расходы по выбранным БО
        foreach ($budgets as $item) {
            // получаем составной номер БО НОМЕР/ГОД
            $num_bo = $item['number'].'/'.$year;
            $payments = \R::find('payment', "num_bo LIKE '%{$num_bo}%'");
            $id = (int)$item['budget_item_id'];
            $bos = \R::getAssoc('SELECT * FROM budget_items WHERE id=?', [$id]);
            $item['payment'] = $this->get_sum($payments, $num_bo, $item['vat']);
            $item['budget_item_name'] = $bos[$id]['name_budget_item'];
        }
        // начинаем работать с AJAX-запросом если включены фильтра
        // если данные пришли AJAX-запросом
        if ($this->isAjax()) {

            $this->loadView('filter', compact('budgets', 'year', 'month'));
        }
        // формируем метатеги для страницы
        $this->setMeta('Список бюджетных операций', 'Описание...', 'Ключевые слова...');
        // Передаем полученные данные в вид
        $this->set(compact('budgets', 'year', 'month'));
    }

    /**
     * Функция подсчитывающая расход по БО
     * @param $payments array Все оплаты содержащие проверяемую БО
     * @param $num_bo string Составной номер БО (НОМЕР/ГОД)
     * @param $vat_bo float|string Ставка НДС проверяемой БО
     * @return float|string Сумма расходов по БО
     */
    private function get_sum($payments, $num_bo, $vat_bo) {
        $sum = 0.00; // расход по данной БО
        foreach ($payments as $payment) { // просматриваем все оплаты использующие нашу БО
            $nums = explode(';', trim($payment['num_bo']));
            $sums = explode(';', trim($payment['sum_bo']));
            $key = array_search($num_bo, $nums);
            if ($vat_bo == '1.20') {
                // если БО с НДС
                if ($payment['vat'] == '1.20') {
                    // если платеж с НДС
                    $sum += $sums[$key];
                }
                if ($payment['vat'] == '1.00') {
                    // если платеж без НДС
                    $sum += round($sums[$key] * 1.2, 2);
                }
            }
            if ($vat_bo == '1.00') {
                // если БО без НДС
                if ($payment['vat'] == '1.00') {
                    // если платеж без НДС
                    $sum += $sums[$key];
                }
                if ($payment['vat'] == '1.20') {
                    // если платеж с НДС
                    $sum += round($sums[$key] / 1.2, 2);
                }
            }            
        }
        return $sum;
    }

    private function get_array_sum($payments, $num_bo, $vat_bo) {
        $partner_obj = New Partner();
        $pay_arr = [];
        foreach ($payments as $payment) {
            $pay['date_pay'] = $payment['date_pay'];
            $nums = explode(';', trim($payment['num_bo']));//->num_bo));
            $sums = explode(';', trim($payment['sum_bo']));//->sum_bo));
            $key = array_search($num_bo, $nums);
            if ($vat_bo == '1.20') {
                // если БО с НДС
                if ($payment['vat'] == '1.20') {
                    // если платеж с НДС
                    $pay['summa'] = $sums[$key];
                }
                if ($payment['vat'] == '1.00') {
                    // если платеж без НДС
                    $pay['summa'] = round($sums[$key] * 1.2, 2);
                }
            }
            if ($vat_bo == '1.00') {
                // если БО без НДС
                if ($payment['vat'] == '1.00') {
                    // если платеж без НДС
                    $pay['summa'] = $sums[$key];
                }
                if ($payment['vat'] == '1.20') {
                    // если платеж с НДС
                    $pay['summa'] = round($sums[$key] / 1.2, 2);
                }
            }
            $pay['partner'] = $partner_obj->getPartnerByID($payment['id_partner']);
            $pay_arr[] = $pay;
        }
        return $pay_arr;
    }

    public function viewAction() {
        $id_bo = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $bo = \R::getRow( 'SELECT budget.id, budget.scenario, budget.month_exp, budget.month_pay, budget.number, budget.summa, budget.vat, budget.budget_item_id, budget.status, budget_items.name_budget_item FROM budget INNER JOIN budget_items ON budget.budget_item_id=budget_items.id WHERE budget.id = ?', [$id_bo]);
        $year = mb_substr($bo['scenario'], 0, 4);
        // формируем составной номер БО (НОМЕР/ГОД)
        $num_bo = $bo['number'].'/'.$year;
        // получаем все оплаты по этой БО
        $budget = new Budget();
        $payments = $budget->getBudgetPayment($num_bo);
        // добавляем в массив оплаченную сумму
        $bo['payment'] = $this->get_sum($payments, $num_bo, $bo['vat']);
        $bo['pay_arr'] = $this->get_array_sum($payments, $num_bo, $bo['vat']);
        // формируем метатеги для страницы
        $this->setMeta("Просмотр бюджетной операции {$bo['number']}", 'Описание...', 'Ключевые слова...');
        // Передаем полученные данные в вид
        $this->set(compact('bo', 'payments'));
    }

    public function editAction() {
        $id_bo = isset($_GET['id']) ? $_GET['id'] : null;
        
        // получаем данные по БО
        $budget = \R::getRow( 'SELECT budget.id, budget.scenario, budget.month_exp, budget.month_pay, budget.number, budget.summa, budget.vat, budget.budget_item_id, budget.status, budget_items.name_budget_item FROM budget INNER JOIN budget_items ON budget.budget_item_id=budget_items.id WHERE budget.id = ?', [$id_bo]);
        // получаем все статьи расхода
        $budget_items = \R::getAll('SELECT * FROM budget_items ORDER BY name_budget_item');
        if ($this->isAjax()) {
            // Если запрос пришел АЯКСом
            $this->loadView('budget_edit_modal', compact('budget', 'budget_items'));
        }
        redirect();
    }

    public function boEditAction() {
        // получаем данные пришедшие методом POST
        $edit_budget = !empty($_POST) ? $_POST : null;
        $_POST['budget_item_id']=(int) $_POST['budget_item_id'];
        $budget = new Budget();
        $budget->load($edit_budget);
        $budget->attributes['budget_item_id'] = (int)$budget->attributes['budget_item_id'];
        $budget->attributes['summa'] = (float)$budget->attributes['summa'];
        $budget->attributes['vat'] = (float)$budget->attributes['vat'];
        $budget->edit('budget', (int)$edit_budget['id']);
        redirect();
    }

    public function uploadAction() {
        unset($_SESSION['success']);
        unset($_SESSION['error']);
        //$this->updateTable();die;

        /*if ($_FILES) {
            //debug($_FILES);
            $path = WWW . '/uploads/' . $_FILES['file']['name'];
            //debug($path);die;
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
                $_SESSION['error'] = 'Ошибка при загрузке файла с данными';
                //header('Location: ../signup.php');
            } else {
                $_SESSION['success'] = 'Вс прошло отлично';
            }
        }*/
        if (isset($_SESSION['file'])) {
            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load(WWW . "/uploads/{$_SESSION['file']}");
            // Только чтение данных
            $reader->setReadDataOnly(true);
            // Количество листов
            $sheetsCount = $spreadsheet->getSheetCount();
            $worksheet = $spreadsheet->getSheetByName('ЯНВАРЬ');

            echo '<table>' . PHP_EOL;
            foreach ($worksheet->getRowIterator() as $row) {
                echo '<tr>' . PHP_EOL;
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(TRUE); // This loops through all cells,
                //    even if a cell value is not set.
                // For 'TRUE', we loop through cells
                //    only when their value is set.
                // If this method is not called,
                //    the default value is 'false'.
                foreach ($cellIterator as $cell) {
                    echo '<td>' .
                        $cell->getValue() .
                        '</td>' . PHP_EOL;
                }
                echo '</tr>' . PHP_EOL;
            }
            echo '</table>' . PHP_EOL;

            /*$data = $sheet->toArray();
            foreach ($data as $item):
                debug($item);
            endforeach;*/

            $_SESSION['success'] = "Все прошло хорошо. В книге {$sheetsCount} листов";
            unlink(WWW . "/uploads/{$_SESSION['file']}"); // удаляем файл
            unset($_SESSION['file']);
        } else {
            $_SESSION['error'] = 'Все очень плохо. Ошибка загрузки файла.';
        }


        //$file = !empty($_POST['file']) ? $_POST['file'] : null;
        // формируем метатеги для страницы
        $this->setMeta('Загрузка новых БО', '', '');
        // Передаем полученные данные в вид
        //$this->set(compact('receipt'));
    }

    public function uploadFileAction() {
        if (!empty($_FILES)) {
            if (isset($_FILES['file'])) {
                // это один файл
                $file = 'file';
            } elseif (isset($_FILES['files'])) {
                // это много файлов
                $file = 'files';
            } else {
                // формируем ответ в виде массива
                $res = [
                    'answer' => 'error',
                    'error' => 'Некорректное имя файла в форме'
                ];
                exit(json_encode($res));
            }
        }
        $path = WWW . '/uploads/';
        $new_name = $this->uploadImg($file, $path);
        if ($file == 'file') {
            $_SESSION['file'] = $new_name;
        } else {
            $_SESSION['files'][] = $new_name;
        }
        $res = array("answer" => "ok", "file" => $new_name);
        exit(json_encode($res));
    }
     public function uploadImg($name, $path/* $wmax, $hmax*/) {
         $ext = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $_FILES[$name]['name'])); // расширение картинки
         $types = array("image/gif", "image/png", "image/jpeg", "image/pjpeg", "image/x-png"); // массив допустимых расширений
         if ($_FILES[$name]['size'] > 5242880) {
             $res = array("error" => "Ошибка! Максимальный вразмер файла - 5Мб!");
             exit(json_encode($res));
         }
         if ($_FILES[$name]['error']) {
             $res = array("answer" => "error", "error" => "Ошибка! Возможно файл слишком большой");
             exit(json_encode($res));
         }
         $new_name = sha1(time()).".$ext";
         $uploadfile = $path.$new_name;
         if (@move_uploaded_file($_FILES[$name]['tmp_name'], $uploadfile)) {
             return $new_name;
         }
     }

    public function fileUploadAction() {

        $file = !empty($_POST['file']) ? $_POST['file'] : null;
        // формируем метатеги для страницы
        $this->setMeta('Загрузка новых БО', '', '');
        // Передаем полученные данные в вид
        //$this->set(compact('receipt'));
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
