<main role="main" class="flex-shrink-0">
    <div class="container">
        <h1 class="mt-1">Добавление/изменение оплат</h1>
        <?php if ($partner) : ?>
            <?php //debug($_SESSION['form_data']); ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?=PATH;?>">Главная</a></li>
                    <li class="breadcrumb-item"><a href="<?=PATH;?>/partner">Список контрагентов</a></li>
                    <li class="breadcrumb-item"><a href="<?=PATH;?>/partner/<?=$partner['inn']; ?>"><?=$partner['name']; ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ввод оплат</li>
                </ol>
            </nav>
            <div class="row d-flex justify-content-center">
                <div class="col-9">
                    <?php if (isset($_SESSION['error_payment'])): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($_SESSION['error_payment'] as $item): ?>
                                    <li><?=$item; ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php unset($_SESSION['error_payment']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row d-flex justify-content-center">
                <div class="col-9">
                    <form method="post" action="receipt/pay-receipt" id="partner_payment" class="was-validated" novalidate>
                        <div class="row g-3">
                            <div class="col-12 has-feedback">
                                <label for="name">Наименование контрагента</label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="Наименование КА" value="<?=isset($_SESSION['form_data']['partner']) ? $_SESSION['form_data']['partner'] : 'Нет данных';?>" disabled>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="date">Дата заявки на оплату</label>
                                <input type="date" name="date" class="form-control" id="date" placeholder="01.01.2021" value="<?=isset($_SESSION['form_data']['date']) ? $_SESSION['form_data']['date'] : '';?>" required>
                                <div class="invalid-feedback">
                                    Введите дату формирования заявки на оплату
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="number">Номер заявки</label>
                                <input type="text" name="number" class="form-control" id="number" placeholder="Номер" value="<?=isset($_SESSION['form_data']['number']) ? $_SESSION['form_data']['number'] : '';?>" required>
                                <div class="invalid-feedback">
                                    Введите номер сформированной заявки на оплату
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="sum_select" id="sum">Сумма оплаты</label>
                                <select name="sum[]" id="sum_select" data-placeholder="Выберите сумму..." class="sum_receipt_select" multiple>
                                    <?php foreach ($receipt_no_pay as $k => $value) : ?>
                                        <option value="<?= $value['summa'];?>" data-number="<?= $value['number'];?>"
                                            <?php //if (in_array(array('number' => $value['number'], 'summa' => $value['summa']), $receipt_select)) echo " selected";?>
                                            <?php if (in_array($value['summa'], $_SESSION['form_data']['sum'])) echo " selected";?>
                                        ><?= $value['summa'];?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Выберите приход для оплаты
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="vat">НДС</label>
                                <select class="form-control" name="vat" id="vat">
                                    <option value="1.20" <?php if ($_SESSION['form_data']['vat'] == '1.20') { echo ' selected';} ?>>20%</option>
                                    <option value="1.00" <?php if ($_SESSION['form_data']['vat'] == '1.00') { echo ' selected';} ?>>Без НДС</option>
                                </select>
                                <div class="invalid-feedback">
                                    Выберите ставку НДС
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="receipt_select">Номера приходов</label><br>
                                <select name="receipt[]" id="receipt_select" data-placeholder="Выберите приход..." class="number_receipt_select" multiple>
                                    <?php foreach ($receipt_no_pay as $k => $value) : ?>
                                        <option value="<?= $value['number'];?>" data-sum="<?= $value['summa'];?>"
                                            <?php //if (in_array(array('number' => $value['number'], 'summa' => $value['summa']), $receipt_select)) echo " selected";?>
                                            <?php if (in_array($value['number'], $_SESSION['form_data']['receipt'])) echo " selected";?>
                                        ><?= $value['number'];?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Выберите приход для оплаты
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="date_pay">Дата оплаты</label>
                                <input type="date" name="date_pay" class="form-control" id="date_pay" placeholder="" value="<?=isset($_SESSION['form_data']['date_pay']) ? $_SESSION['form_data']['date_pay'] : '';?>" required>
                                <div class="invalid-feedback">
                                    Введите дату предпологаемой оплаты
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="num_er">Номер ЕР</label><br>
                                <select name="num_er[]" id="num_er" data-placeholder="Выберите ЕР..." class="num_er_select" multiple>
                                    <?php foreach ($ers as $k => $v) : ?>
                                        <optgroup label="<?= $v['budget'];?>">
                                            <option value="<?= $v['number'];?>"
                                                <?php
                                                //if (isset($ers_sel)) {
                                                if (isset($_SESSION['form_data']['num_er'])) {
                                                    foreach ($_SESSION['form_data']['num_er'] as $er) {
                                                        //if ($er['number'] == $v['number']) {
                                                        if ($er == $v['number']) {
                                                            echo " selected";
                                                        }
                                                    }
                                                }
                                                ?>
                                            ><?= $v['number'];?></option>
                                        </optgroup>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Выберите ЕР которые служат для оплаты
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="sum_er">Сумма ЕР</label>
                                <?php
                                /*$sum_str = '';
                                if (isset($ers_sel)) {
                                    foreach ($ers_sel as $er) {
                                        $sum_str .= $er['summa'] . ';';
                                    }
                                    $sum_str = rtrim($sum_str, ';');
                                }*/
                                ?>
                                <input type="text" name="sum_er" class="form-control" id="sum_er" placeholder="" value="<?=isset($_SESSION['form_data']['sum_er']) ? $_SESSION['form_data']['sum_er'] : '';?>" required>
                                <div class="invalid-feedback">
                                    Введите суммы для оплаты
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="num_bo">Номер БО</label>
                                <input type="text" name="num_bo" class="form-control" id="num_bo"  placeholder="Номер документа" value="<?=isset($_SESSION['form_data']['num_bo']) ? $_SESSION['form_data']['num_bo'] : '';?>" required>
                                <div class="invalid-feedback">
                                    Введите номера БО используемых для оплаты
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="sum_bo">Сумма БО</label>
                                <input type="text" name="sum_bo" class="form-control" id="sum_bo" placeholder="" value="<?=isset($_SESSION['form_data']['sum_bo']) ? $_SESSION['form_data']['sum_bo'] : '';?>" required>
                                <div class="invalid-feedback">
                                    Введите суммы БО используемых для оплаты
                                </div>
                            </div>
                            <input type="hidden" name="id_partner" value="<?=isset($_SESSION['form_data']['id_partner']) ? $_SESSION['form_data']['id_partner'] : '';?>">
                            <input type="hidden" name="id" value="<?=isset($payments['id']) ? $payments['id'] : '';?>">
                            <input type="hidden" name="inn" value="<?=isset($_SESSION['form_data']['inn']) ? $_SESSION['form_data']['inn'] : '';?>">
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary mt-3">Создать оплату</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php else : ?>
            <h3>Отсутствуют данные для обработки</h3>
        <?php endif; ?>
    </div>
</main>
<script type="text/javascript" src="assets/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="assets/chosen/docsupport/prism.js"></script>
<script type="text/javascript" src="assets/chosen/docsupport/init.js"></script>
<script>
    $(function () {
        $(".number_receipt_select").chosen({
            width: "100%"
        });
        $(".num_er_select").chosen({
            width: "100%"
        });
        $(".sum_receipt_select").chosen({
            width: "100%"
        });
        $("#sum_select").change(function() {
            const ids = $(this).val();
            let sum = 0;
            for(let i = 0; i < ids.length; i++) {
                let $select = $(this);
                console.log($select.children().eq(i).data('number'));
                sum += parseFloat(ids[i]);
            }
            if ($('#sum_er').val().length < 5) {
                $('#sum_er').val(sum.toFixed(2));
            }
            if ($('#sum_bo').val().length < 5) {
                $('#sum_bo').val(sum.toFixed(2));
            }
        });
    });
</script>
