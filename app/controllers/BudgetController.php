<?php

namespace app\controllers;

use app\models\Budget;
use app\models\Partner;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RedBeanPHP\R;

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
        unset($_SESSION['success']); // удаляем сессию с успешными сообщениями
        unset($_SESSION['error']);   // удаляем сессию с ошибочными сообщениями
        
        if (isset($_SESSION['file'])) {
            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load(WWW . "/uploads/{$_SESSION['file']}");
            $reader->setReadDataOnly(true);                 // Устанавливаем только чтение данных
            $worksheet = $spreadsheet->getActiveSheet();    // считываем данные из активного листа
            unlink(WWW . "/uploads/{$_SESSION['file']}");   // удаляем файл. После доработки перенести удаление в конец            
            unset($_SESSION['file']);                       // удаляем сессию с именем обрабатываемого файла
            $header = true;     // ключ что это строка заголовков
            $header_array = []; // массив содержащий заголовки столбцов
            $row_array = [];    // массив со значениями текущей строки
            $budget_array = []; // массив со значениями текущей строки
            /* упорядочиваем полученные данные - начало */
            foreach ($worksheet->getRowIterator() as $row) {
                // обрабатываем очередную строку из файла
                $i = 0;
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(TRUE); // просматривать только заполненные ячейки
                foreach ($cellIterator as $cell) {
                    if ($header)  { // если это заголовок заполняем массив 
                        $header_array[] = $cell->getValue(); // получаем наименования столбцов в массив
                    } else {        // если это строка с данными
                        $row_array[$header_array[$i]] = $cell->getValue();
                        $i = $i + 1;
                    }
                }
                if (!$header) $budget_array[] = $row_array; // если это не заголовок добавляем в массив данных
                $header = false;
            }
            /***  упорядочиваем полученные данные - конец  ***/
            /* массив $budget_array содержит данные из файла */
            /* преобразование данных для добавление в БД - начало */
            $budget_obj = new Budget(); // экземпляр модели Budget
            $budget_success = []; $success = true;
            $_SESSION['error'] = "";
            $i = 2;
            foreach ($budget_array as $item) {
                // Обрабатываем только строки со статусом СОГЛАСОВАН и НА СОГЛАСОВАНИИ
                if ($item['Статус документа'] != 'Не согласован' && $item['Статус документа'] != 'Формирование') {
                    $bo['scenario'] = $spreadsheet->getActiveSheet()->getTitle();  // сценарий берем из имени листа
                    $bo['month_exp'] = $this->dateChange($item['Месяц расходов']);
                    $bo['month_pay'] = $this->dateChange($item['Месяц оплаты']);
                    $bo['number'] = $item['Номер'];
                    $bo['summa'] = $item['Сумма'];
                    if ($item['Ставка НДС'] == 'Без НДС') {
                        $bo['vat'] = '1.00';
                    } elseif ($item['Ставка НДС'] == '20%') {
                        $bo['vat'] = '1.20';
                    } else {
                        $bo['vat'] = '1.10';
                    }                
                    $bo_item = R::getAssocRow("SELECT * FROM budget_items WHERE name_budget_item = ?", [$item['Статья бюджета']]);                    
                    if ($bo_item) {
                        $bo_item = $bo_item[0];
                        $bo['budget_item_id'] = $bo_item['id'];
                    } else {
                        $success = false;
                        $_SESSION['error'] .= "Все очень плохо. В БД отсутствует запись - {$item['Статья бюджета']}. Строка - {$i} ";
                        $this->setMeta('Загрузка новых БО', '', '');
                        return;
                    }                    
                    $bo['status'] = $item['Статус документа'];
                    $budget_success[] = $bo;                    
                } 
                $i += 1;
            }
            $i = 0; $y = 0; 
            if ($success) {
                $_SESSION['success'] = "Все прошло хорошо. ";
                foreach ($budget_success as $item) {
                    $budget_obj->load($item);
                    // проверка на наличии БО с этим номером в БД
                    if (!$this->checkBO($item)) {
                        // БО нет в БД
                        $budget_obj->save('budget');
                        $i = $i + 1;
                    } else {
                        $id = $this->checkBO($item);
                        $budget_obj->edit('budget', $id);
                        $y = $y + 1;
                    }
                    unset($_SESSION['error']);
                } 
                if ($i > 0) $_SESSION['success'] .= "Добавлено {$i} бюджетных операций. ";
                if ($y > 0) $_SESSION['success'] .= "Исправлено {$y} бюджетных операций. ";
            } else {
                $_SESSION['error'] .= 'Все очень плохо. Ошибка загрузки файла. ';
            }           
            
        } 
        
        // формируем метатеги для страницы
        $this->setMeta('Загрузка новых БО', '', '');
    }

    public function checkBO($data) {
        $bo_item = R::getAssocRow("SELECT * FROM budget WHERE scenario = ? AND number = ?", [$data['scenario'], $data['number']]);
        if ($bo_item) {
            $bo_item = $bo_item[0];
            return $bo_item['id'];
        } else {
            return false;
        }
    }
    
    public function dateChange($date_str) {
        $year = substr($date_str, 6, 4);
        $month = substr($date_str, 3, 2);
        $day = substr($date_str, 0, 2);
        return $year . '-' . $month . '-' . $day;
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

}
