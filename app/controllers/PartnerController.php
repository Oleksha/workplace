<?php

namespace app\controllers;

use app\models\Budget;
use app\models\Er;
use app\models\Partner;
use app\models\Payment;
use app\models\Receipt;
use Exception;

class PartnerController extends AppController {

    public function indexAction() {
        // создаем необходимые объекты связи с БД
        $partner_obj = new Partner(); // для КА
        $er_obj = new Er();           // для ЕР
        $receipt_obj = new Receipt(); // для приходов
        // получаем информацию о КА
        $partners = $partner_obj->getPartner('name');
        // Получение дополнительную информацию о КА
        $partners_ext = [];
        foreach ($partners as $partner) {
            // Получаем количество действующих ЕР
            $ers = $er_obj->getCurrentEr($partner['id']);
            $partner['er'] = $ers ? count($ers) : 0;
            $sum = 0;
            // Получаем сумму дебиторской задолженности
            $receipts = $receipt_obj->getReceiptNoPay($partner['id']);
            if ($receipts) {
                foreach ($receipts as $receipt) {
                    $sum += $receipt['sum'];
                }
            }
            $partner['sum'] = $sum;
            $partners_ext[] = $partner;
        }
        $partners = $partners_ext;
        // формируем метатеги для страницы
        $this->setMeta('Cписок активных контрагентов', 'Содержит список активных КА с дополнительной информацией о каждом', 'контрагент,дебиторка,задолженность,отсрочка,ер,единоличные,решения');
        // Передаем полученные данные в вид
        $this->set(compact('partners'));
    }

    /**
     * Если контрагента несуществует
     * @throws Exception
     */
    public function viewAction() {
        // создаем необходимые объекты связи с БД
        $partner_obj = new Partner(); // для КА
        $er_obj = new Er();           // для ЕР
        $receipt_obj = new Receipt(); // для приходов
        // получение ИНН запрашиваемого контрагента
        $inn = $this->route['inn'];
        // получение данных по КА из БД
        $partner = $partner_obj->getPartnerByINN($inn);
        if (!$partner) {
            // Если такого контрагента нет выбрасываем исключение
            throw new Exception('Контрагент с ИНН ' . $inn . ' не найден', 500);
        }
        // ЕДИНОЛИЧНЫЕ РЕШЕНИЯ
        // Получаем все ЕР для КА действующие на данный момент
        // получение данных по ЕР для КА из БД
        $ers = $er_obj->getCurrentEr($partner['id']);
        $ers_all = $er_obj->getAllEr($partner['id']);
        $diff = $this->my_array_diff($ers_all, $ers);
        // добавляем в массив данные по расходам этого ЕР
        if ($ers) $ers = $this->costs($ers, $partner['vat'], $partner['id']);
        // ПРИХОДЫ
        $receipt = $receipt_obj->getReceipt('id_partner', $partner['id']);
        // формируем метатеги для страницы
        $this->setMeta($partner['name'], 'Описание...', 'Ключевые слова...');
        // Передаем полученные данные в вид
        $this->set(compact('partner', 'ers', 'diff', 'receipt'));
    }

    public function my_array_diff(&$arr1, &$arr2): array {
        $diff = [];
        if(is_array($arr1) and is_array($arr2)) {
            foreach ($arr1 as $item) {
                $key = false;
                foreach ($arr2 as $value) {
                    if ($item['number'] === $value['number']) {
                        $key = true;
                        break;
                    }
                }
                if (!$key) {
                    $diff[] = $item;
                }
            }
        }
        return $diff;
    }

    /**
     * @param $ers array список ЕР
     * @param $vat double ставка НДС
     * @param $id string идентификатор КА
     * @return array список ЕР с остатками средств
     */
    public function costs($ers, $vat, $id) {
        $pay_obj = new Payment();
        foreach ($ers as $k => $er) {
            // получаем все оплаты использующие эту ЕР
            $payments = $pay_obj->getPaymentEr($id, $er['number']);
            // проходимся по каждому приходу чтобы получить суммы расхода по данной ЕР
            /* создаем массив в виде
                [0] [
                    number: 00000000,
                    summa: 123.12
                */
            $sum = 0.00;
            if ($payments) {
                foreach ($payments as $payment) {
                    // каждая оплата может использовать несколько ЕР
                    $nums = explode(';', $payment['num_er']);
                    $sums = explode(';', $payment['sum_er']);
                    $key = array_search($er['number'], $nums);
                    $sum += $sums[$key];
                }
                $sum = round($sum / $vat, 2);
            }
            $ers[$k]['cost'] = $sum;
        }
        return $ers;
    }

    public function addAction() {
        unset($_SESSION['form_data']); // Очищаем сессию данных о КА
        // если существуют переданные данные методом POST
        if (!empty($_POST)) {
            // создаем объект класса-модели User
            $user = new Partner();
            // запоминаем переданные данные
            $data = $_POST;
            // загружаем переданные данные
            $user->load($data);
            // проверяем заполненные данные
            if (!$user->checkUnique()) {
                // если проверка не пройдена запишем ошибки в сессию
                $user->getErrors();
                // запоминаем уже введенные данные
                $_SESSION['form_data'] = $data;
            } else {
                $user->attributes['delay'] = (int)$user->attributes['delay'];
                $user->attributes['vat'] = (double)$user->attributes['vat'];
                // если проверка пройдена записываем данные в таблицу
                if ($id = $user->save('partner')) {
                    // если все прошло хорошо в ID номер зарегистрированного пользователя
                    //$_SESSION['success'] = 'Контрагент сохранен в БД';
                    unset($_SESSION['form_data']);
                    // перезагрузим страницу
                    redirect("/partner/" . $data['inn']);
                } else {
                    $_SESSION['errors'] = 'Возникли ошибки при сохранении данных в БД';
                }
            }

        }
        // устанавливаем метаданные
        $this->setMeta('Добавление нового контрагента');

    }

    public function editAction() {
        // очищаем сессию если она существует
        unset($_SESSION['form_data']);
        // получаем переданный идентификатор КА
        $id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
        if ($id) {
            // если у нас есть ID получаем все данные об этом KA
            $ka_arr = new Partner();
            //$partner = \R::findOne('partner', 'id = ?', [$id]);
            $partner = $ka_arr->getPartnerByID($id);
            if (!$partner) return false; // если RF не найден дальнейшие действия бессмысленны
            // запоминаем полученные данные
            $_SESSION['form_data'] = $partner;
        }
        if ($this->isAjax()) {
            // Если запрос пришел АЯКСом
            $this->loadView('ka_edit_modal');

        }
        redirect();
    }

    public function editKaAction() {
        // получаем данные пришедшие методом POST
        $edit_ka = !empty($_POST) ? $_POST : null;
        //debug($edit_ka);
        $partner = new Partner();
        $partner->load($edit_ka);
        $partner->edit('partner', $edit_ka['id_ka']);
        unset($_SESSION['form_data']);
        redirect();
    }

    public function paymentAction() {
        //unset($_SESSION['form_data']);die;
        // получаем переданные GET данные
        $id_receipt = !empty($_GET['receipt']) ? (int)$_GET['receipt'] : null; // идентификатор прихода
        // создаем объекты для работы с БД
        $receipt_obj = new Receipt(); // для приходов
        $partner_obj = new Partner(); // для КА
        $er_obj = new Er();           // для ЕР
        $payment_obj = new Payment(); // для ЗО
        $partner = null; $receipt = null;
        if (!$id_receipt) redirect();
        $receipt = $receipt_obj->getReceipt('id', $id_receipt);
        $receipt = $receipt[0];
        $partner = $partner_obj->getPartnerByID($receipt['id_partner']);
        // получаем ставка НДС по которой работает КА
        $vat = !empty($partner['vat']) ? $partner['vat'] : null;
        $pay_key = !is_null($receipt['date_pay']); // индикатор оплаты прихода
        /***** Начало получения данных для формирования заявки на оплату (ЗО) ******/
        /* Получаем все действующие ЕР для этого КА на момент прихода */
        $ers = $er_obj->getCurrentErFromDate($partner['id'], $receipt['date']);

        $er = [];
        foreach ($ers as $k => $v) {
            $er[$k]['budget'] = $v['name_budget_item'];
            $er[$k]['number'] = $v['number'];
        }
        $ers = $er;
        /* Проверяем есть ли у этого прихода завка на оплату (ЗО) */
        $name = $partner['name'];                                 // Имя КА (ВСС ООО)
        $year = date('Y', strtotime($receipt['date']));       // Получаем год прихода (2022)
        $receipt_num = '%' . $receipt['number'] . '/' . $year . '%'; // Получаем используемый номер прихода (TOF00000000/2022)
        //$payments = \R::findOne('payment', "receipt LIKE ?", [$receipt_num]);   // Получаем заявку на оплату для этого прихода (если есть)
        $payments = $payment_obj->getPaymentFromReceipt($receipt_num);  // Получаем заявку на оплату для этого прихода (если есть)
        if ($payments) $payments = $payments[0];
        //$receipts = \R::find('receipt', 'partner = ? AND date_pay IS NULL ORDER BY date', [$name]); // Получаем все неоплаченные приходы этого КА
        $receipts = $receipt_obj->getReceiptNoPay($partner['id']); // Получаем все неоплаченные приходы этого КА
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
        // заполняем сессию с полями формы для заполнения
        $_SESSION['form_data']['vat'] = $vat;
        $_SESSION['form_data']['partner'] = $partner['name'];
        $_SESSION['form_data']['id_partner'] = $partner['id'];
        $_SESSION['form_data']['inn'] = $partner['inn'];
        if (!$payments) {
            // Если ЗО нет (режим добавления)
            $receipt_select['0']['number'] = trim($receipt_num, '%');
            $receipt_select['0']['summa'] = $receipt['sum'];
            // заполняем сессию с полями формы для заполнения
            $_SESSION['form_data']['sum'][0] = $receipt['sum'];
            $_SESSION['form_data']['receipt'][0] = trim($receipt_num, '%');
        } else {
            // заполняем сессию с полями формы для заполнения
            $_SESSION['form_data']['num_er'] = isset($_SESSION['form_data']['num_er']) ? $_SESSION['form_data']['num_er'] : explode(';', $payments['num_er']);
            $_SESSION['form_data']['sum_er'] = isset($_SESSION['form_data']['sum_er']) ? $_SESSION['form_data']['sum_er'] : $payments['sum_er'];
            $_SESSION['form_data']['date'] = isset($_SESSION['form_data']['date']) ? $_SESSION['form_data']['date'] : $payments['date'];
            $_SESSION['form_data']['number'] = isset($_SESSION['form_data']['number']) ? $_SESSION['form_data']['number'] : $payments['number'];
            $_SESSION['form_data']['id'] = $payments['id'];
            $_SESSION['form_data']['date_pay'] = isset($_SESSION['form_data']['date_pay']) ? $_SESSION['form_data']['date_pay'] : $payments['date_pay'];
            $_SESSION['form_data']['num_bo'] = isset($_SESSION['form_data']['num_bo']) ? $_SESSION['form_data']['num_bo'] : $payments['num_bo'];
            $_SESSION['form_data']['sum_bo'] = isset($_SESSION['form_data']['sum_bo']) ? $_SESSION['form_data']['sum_bo'] : $payments['sum_bo'];
            $er_sel = explode(';', $payments['num_er']); // выбранные ер
            $er_sum = explode(';', $payments['sum_er']); // суммы выбранных ер
            foreach ($er_sel as  $k => $v) {
                $new_er[$k]['number'] = $v;
                $new_er[$k]['summa'] = $er_sum[$k];
            }
            $ers_sel = $new_er;
            $recs = explode(';', $payments['receipt']); // доступные приходы
            $sums = explode(';', $payments['sum']); // все выбранные приходы
            $_SESSION['form_data']['sum'] = isset($_SESSION['form_data']['sum']) ? $_SESSION['form_data']['sum'] : explode(';', $payments['sum']);
            $_SESSION['form_data']['receipt'] = isset($_SESSION['form_data']['receipt']) ? $_SESSION['form_data']['receipt'] : explode(';', $payments['receipt']);
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


        //debug($_SESSION['form_data']);die;

        // формируем метатеги для страницы
        $this->setMeta('Введение оплат', 'Введение заявки на оплату приходов текущего КА', '');
        // Передаем полученные данные в вид
        $this->set(compact('partner', 'receipt_select', 'receipt_no_pay', 'ers', 'ers_sel', 'payments', 'vat'));

    }

}
