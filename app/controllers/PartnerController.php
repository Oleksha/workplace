<?php

namespace app\controllers;

use app\models\Er;
use app\models\Partner;
use app\models\Payment;
use app\models\Receipt;
use Exception;

class PartnerController extends AppController {

    /**
     * Обрабатывает Выдачу информации обо всех КА
     * @return void
     */
    public function indexAction() {
        // создаем необходимые объекты связи с БД
        $partner_models = new Partner(); // Контрагенты
        $er_models = new Er();           // Единоличные решения
        $receipt_models = new Receipt(); // Поступления товаров и услуг
        // получаем информацию обо всех КА
        $partners = $partner_models->getPartnerAll('name');
        foreach ($partners as $k => $partner) {
            // Получаем количество действующих ЕР
            $ers = $er_models->getERToday($partner['id']);
            $partners[$k]['er'] = $ers ? count($ers) : 0;
            // Получаем сумму дебиторской задолженности
            $sum = 0;
            $receipts = $receipt_models->getReceiptNoPay($partner['id']); // получаем неоплаченные поступления
            if ($receipts) foreach ($receipts as $receipt) $sum += $receipt['sum']; // подсчитываем сумму задолженности
            $partners[$k]['sum'] = $sum;
        }
        // формируем метатеги для страницы
        $this->setMeta('Cписок активных контрагентов', 'Содержит список активных КА с дополнительной информацией о каждом', 'контрагент,дебиторка,задолженность,отсрочка,ер,единоличные,решения');
        // Передаем полученные данные в вид
        $this->set(compact('partners'));
    }

    /**
     * Обрабатывает Выдачу информации о выбранном КА
     * @return void
     * @throws Exception
     */
    public function viewAction() {
        // создаем необходимые объекты связи с БД
        $partner_models = new Partner(); // Контрагенты
        $er_models = new Er();           // Единоличные решения
        $receipt_models = new Receipt(); // для приходов
        // получение ID запрашиваемого контрагента
        $id = $this->route['id'];
        // получение данных по КА из БД
        $partner = $partner_models->getPartner($id);
        if (!$partner) throw new Exception('Контрагент с ID ' . $id . ' не найден', 500);
        // ЕДИНОЛИЧНЫЕ РЕШЕНИЯ
        // Получаем все ЕР для КА
        $ers = $er_models->getERToday($partner['id']);      // действующие на данный момент
        $ers_all = $er_models->getERAll($partner['id']);    // все ЕР в базе данных
        $diff = my_array_diff($ers_all, $ers); // недействующие
        // добавляем в массив данные по расходам этого ЕР
        if ($ers) {
            foreach ($ers as $k => $er) {
                // получаем расходы по этой ЕР
                $ers[$k]['costs'] = $er_models->getERCosts($er['id']);
            }
        }
        // ПРИХОДЫ
        $receipt = $receipt_models->getReceipt('id_partner', $id);
        if ($receipt) {
            foreach ($receipt as $k => $v) {
                $receipt[$k]['type'] = $receipt_models->isTypeReceipt($v['id']);
            }
        }
        // формируем метатеги для страницы
        $this->setMeta($partner['name'],'Наименование КА', 'Описание...', 'Ключевые слова...');
        // Передаем полученные данные в вид
        $this->set(compact('partner', 'ers', 'diff', 'receipt'));
    }

    /**
     * Добавляет в массив ЕР расход по ЕР
     * @param $ers array список ЕР
     * @param $vat double ставка НДС
     * @param $id int идентификатор КА
     * @return array
     */
    public function costs(array $ers, float $vat, int $id): array {
        $er_models = new Er();           // Единоличные решения
        foreach ($ers as $k => $er) {
            // получаем расходы по этой ЕР
            $ers[$k]['costs'] = $er_models->getERCosts($er['id']);
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
                    redirect("/partner/" . $id);
                } else {
                    $_SESSION['errors'] = 'Возникли ошибки при сохранении данных в БД';
                }
            }

        }
        // устанавливаем метаданные
        $this->setMeta('Добавление нового контрагента');

    }

    /**
     * Изменяет данные о КА
     * @return false|void
     */
    public function editAction() {
        // очищаем сессию чтобы в ней небыло никаких данных
        unset($_SESSION['form_data']);
        // получаем переданный идентификатор КА
        $id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
        if ($id) {
            // если у нас есть ID получаем все данные об этом KA
            $partner_models = new Partner(); // Контрагенты
            $partner = $partner_models->getPartner($id);
            if (!$partner) return false; // если КА не найден дальнейшие действия бессмысленны
            // запоминаем полученные данные
            $_SESSION['form_data'] = $partner;
        }
        if ($this->isAjax()) {
            // Если запрос пришел АЯКСом
            $this->loadView('ka_edit_modal');

        }
        redirect();
    }

    /**
     * Сохраняет данные о КА после изменения
     * @return void
     */
    public function editPartnerAction() {
        // получаем данные пришедшие методом POST
        $data = !empty($_POST) ? $_POST : null;
        $partner_models = new Partner(); // Контрагенты
        $partner_models->load($data);
        $partner_models->edit('partner', $data['partner_id']);
        unset($_SESSION['form_data']); // очищаем сессию чтобы в ней небыло никаких данных
        redirect();
    }

    /**
     * Обработка заявки на оплату
     * @return void
     */
    public function paymentAction() {
        // получаем переданные GET данные
        $receipt_id = !empty($_GET['receipt']) ? (int)$_GET['receipt'] : null; // идентификатор прихода
        $type = !empty($_GET['type']) ? (int)$_GET['type'] : null; // тип выводимой информации
        // создаем объекты для работы с БД
        $receipt_model = new Receipt(); // для приходов
        $partner_model = new Partner(); // для КА
        $er_model = new Er();           // для ЕР
        $payment_model = new Payment(); // для ЗО
        $payment = []; // Если ЗО есть получаем данные о ней
        /*if ($type == 1 || $type == 2)*/ $payment = $payment_model->getPaymentReceipt($receipt_id);
        // получаем всю информацию о текущем поступлении
        $receipt = $receipt_model->getReceipt('id', $receipt_id);
        $receipt = $receipt[0];
        $receipt_all = []; // получаем информацию о неоплаченных поступлениях
        /*if ($type == 2 || $type == 3)*/ $receipt_all = $receipt_model->getReceiptNoPay($receipt['id_partner']);
        // получаем всю информацию о КА
        //debug($receipt_all);die;
        $partner = $partner_model->getPartner($receipt['id_partner']);
        /* Получаем все действующие ЕР для этого КА на момент прихода */
        $ers = $er_model->getERFromDatePayment($partner['id'], $receipt['date']);
        $er = [];
        foreach ($ers as $k => $v) {
            $er[$k]['id'] = $v['id'];                                             // идентификатор
            $er[$k]['budget'] = $v['name_budget_item'];                           // статья расхода
            $er[$k]['number'] = $v['number'];                                     // номер ЕР
        }
        $ers = $er;
        // формируем метатеги для страницы
        $this->setMeta('Заявка на оплату - ' . $partner['name'],'Заявка на оплату', 'Описание...', 'Ключевые слова...');
        // Передаем полученные данные в вид
        $this->set(compact('payment','receipt', 'receipt_all', 'partner', 'ers', 'type'));

    }

}
