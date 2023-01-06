<main role="main" class="flex-shrink-0">
    <div class="container">
        <h1 class="mt-1">Заявка на оплату</h1>
        <?php if ($partner) : ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?=PATH;?>">Главная</a></li>
                    <li class="breadcrumb-item"><a href="<?=PATH;?>/partner">Список контрагентов</a></li>
                    <li class="breadcrumb-item"><a href="<?=PATH;?>/partner/<?=$partner['id']; ?>"><?=$partner['name']; ?></a></li>
                    <?php if ($type == 1) :?>
                        <li class="breadcrumb-item active" aria-current="page">Просмотр ЗО</li>
                    <?php elseif ($type == 2) :?>
                        <li class="breadcrumb-item active" aria-current="page">Изменение ЗО</li>
                    <?php else :?>
                        <li class="breadcrumb-item active" aria-current="page">Ввод оплат</li>
                    <?php endif;?>
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
                                <input type="text" name="name" class="form-control" id="name" placeholder="Наименование КА" value="<?= $partner['name'] ?? 'Нет данных';?>" disabled>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="date">Дата заявки на оплату</label>
                                <input type="date" name="date" class="form-control" id="date" placeholder="01.01.2021" value="<?= $_SESSION['form_data']['date'] ?? ($payment['date'] ?? '');?>" <?= $type == 1 ? 'disabled' : '' ?> required>
                                <div class="invalid-feedback">
                                    Введите дату формирования заявки на оплату
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="number">Номер заявки</label>
                                <input type="text" name="number" class="form-control" id="number" placeholder="Номер" value="<?= $_SESSION['form_data']['number'] ?? ($payment['number'] ?? '');?>" <?= $type == 1 ? 'disabled' : '' ?> required>
                                <div class="invalid-feedback">
                                    Введите номер сформированной заявки на оплату
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="sum_select" id="sum">Сумма оплаты</label>
                                <select name="sum[]" id="sum_select" data-placeholder="Выберите сумму..." class="sum_receipt_select" <?= $type == 1 ? 'disabled' : '' ?> multiple>
                                    <?php if ($_SESSION['form_data']['sum']) :?>
                                        <?php foreach ($receipt_all as $k => $value) : ?>
                                            <option value="<?= $value['sum'];?>"
                                            <?php if (in_array($value['sum'], $_SESSION['form_data']['sum'])) echo " selected";?>
                                            ><?= $value['sum'];?></option>
                                        <?php endforeach; ?>
                                    <?php else :?>
                                        <?php if ($type == 1) :?>
                                            <?php foreach (explode(';', $payment['sum']) as $value) : ?>
                                                <option value="<?= $value;?>"
                                                    selected
                                                ><?= $value;?></option>
                                            <?php endforeach; ?>
                                        <?php elseif ($type == 2) : ?>
                                            <?php foreach ($receipt_all as $k => $value) : ?>
                                                <option value="<?= $value['sum'];?>" data-id="<?= $value['id'];?>"
                                                    <?php if (in_array($value['id'], explode(';', $payment['receipts_id']))) echo " selected";?>
                                                ><?= $value['sum'];?></option>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <?php foreach ($receipt_all as $k => $value) : ?>
                                                <option value="<?= $value['sum'];?>" data-id="<?= $value['id'];?>"
                                                    <?php if (in_array($value['id'], explode(';', $receipt['id']))) echo " selected";?>
                                                ><?= $value['sum'];?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Выберите оплачиваемую сумму
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="vat">НДС</label>
                                <select class="form-control" name="vat" id="vat" <?= $type == 1 ? 'disabled' : '' ?>>
                                    <?php if ($_SESSION['form_data']['vat']) :?>
                                        <option value="1.20" <?php if ($_SESSION['form_data']['vat'] == '1.20') { echo ' selected';} ?>>20%</option>
                                        <option value="1.00" <?php if ($_SESSION['form_data']['vat'] == '1.00') { echo ' selected';} ?>>Без НДС</option>
                                    <?php else :?>
                                        <?php if ($payment) : ?>
                                            <option value="1.20" <?php if ($payment['vat'] == '1.20') { echo ' selected';} ?>>20%</option>
                                            <option value="1.00" <?php if ($payment['vat'] == '1.00') { echo ' selected';} ?>>Без НДС</option>
                                        <?php else : ?>
                                            <option value="1.20" <?php if ($partner['vat'] == '1.20') { echo ' selected';} ?>>20%</option>
                                            <option value="1.00" <?php if ($partner['vat'] == '1.00') { echo ' selected';} ?>>Без НДС</option>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Выберите ставку НДС
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="receipt_select">Номера приходов</label><br>
                                <select name="receipt[]" id="receipt_select" data-placeholder="Выберите приход..." class="number_receipt_select" multiple <?= $type == 1 ? 'disabled' : '' ?>>
                                    <?php if ($_SESSION['form_data']['receipt']) :?>
                                        <?php foreach ($receipt_all as $k => $value) : ?>
                                            <option value="<?= $value['id'];?>"
                                                <?php if (in_array($value['id'], $_SESSION['form_data']['receipt'])) echo " selected";?>
                                            ><?= $value['number'];?></option>
                                        <?php endforeach; ?>
                                    <?php else :?>
                                        <?php if ($type == 1) :?>
                                            <?php foreach (explode(';', $payment['receipt']) as $value) : ?>
                                                <option value="<?= $value;?>" selected><?= $value;?></option>
                                            <?php endforeach; ?>
                                        <?php elseif ($type == 2) :?>
                                            <?php foreach ($receipt_all as $k => $value) : ?>
                                                <option value="<?= $value['id'];?>"
                                                    <?php if (in_array($value['id'], explode(';', $payment['receipts_id']))) echo " selected";?>
                                                ><?= $value['number'];?></option>
                                            <?php endforeach; ?>
                                        <?php else :?>
                                            <?php foreach ($receipt_all as $k => $value) : ?>
                                                <option value="<?= $value['id'];?>"
                                                    <?php if (in_array($value['id'], explode(';', $receipt['id']))) echo " selected";?>
                                                ><?= $value['number'];?></option>
                                            <?php endforeach; ?>
                                        <?php endif;?>
                                    <?php endif;?>
                                </select>
                                <div class="invalid-feedback">
                                    Выберите приход для оплаты
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="date_pay">Дата оплаты</label>
                                <input type="date" name="date_pay" class="form-control" id="date_pay" placeholder="" value="<?= $_SESSION['form_data']['date_pay'] ?? ($payment['date_pay'] ?? '');?>" required <?= $type == 1 ? 'disabled' : '' ?>>
                                <div class="invalid-feedback">
                                    Введите дату предпологаемой оплаты
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="num_er">Номер ЕР</label><br>
                                <select name="num_er[]" id="num_er" data-placeholder="Выберите ЕР..." class="num_er_select" multiple <?= $type == 1 ? 'disabled' : '' ?>>
                                    <?php if ($_SESSION['form_data']['num_er']) :?>
                                        <?php foreach ($ers as $k => $v) : ?>
                                            <optgroup label="<?= $v['budget'];?>">
                                                <option value="<?= $v['id'];?>"
                                                    <?php if (in_array($v['id'], $_SESSION['form_data']['num_er'])) echo " selected"; ?>
                                                ><?= $v['number'];?></option>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    <?php else :?>
                                        <?php if ($type == 1) :?>
                                            <?php foreach (explode(';', $payment['num_er']) as $item) : ?>
                                                <option value="<?= $item;?>" selected><?= $item;?></option>
                                            <?php endforeach; ?>
                                        <?php elseif ($type == 2) :?>
                                            <?php foreach ($ers as $k => $v) : ?>
                                                <optgroup label="<?= $v['budget'];?>">
                                                    <option value="<?= $v['id'];?>"
                                                        <?php if (in_array($v['id'], explode(';', $payment['ers_id']))) echo " selected"; ?>
                                                    ><?= $v['number'];?></option>
                                                </optgroup>
                                            <?php endforeach; ?>
                                        <?php else :?>
                                            <?php foreach ($ers as $k => $v) : ?>
                                                <optgroup label="<?= $v['budget'];?>">
                                                    <option value="<?= $v['id'];?>"><?= $v['number'];?></option>
                                                </optgroup>
                                            <?php endforeach; ?>
                                        <?php endif;?>
                                    <?php endif;?>
                                </select>
                                <div class="invalid-feedback">
                                    Выберите ЕР которые служат для оплаты
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="sum_er">Сумма ЕР</label>
                                <?php if (isset($_SESSION['form_data']['sum_er'])) {
                                    $str = implode(';', $_SESSION['form_data']['sum_er']);
                                }?>
                                <input type="text" name="sum_er[]" class="form-control" id="sum_er" placeholder="" value="<?=$str ?? ($payment['sum_er'] ?? '');?>" required <?= $type == 1 ? 'disabled' : '' ?>>
                                <div class="invalid-feedback">
                                    Введите суммы для оплаты
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="num_bo">Номер БО</label>
                                <input type="text" name="num_bo" class="form-control" id="num_bo"  placeholder="Номер документа" value="<?=$_SESSION['form_data']['num_bo'] ?? ($payment['num_bo'] ?? '');?>" required <?= $type == 1 ? 'disabled' : '' ?>>
                                <div class="invalid-feedback">
                                    Введите номера БО используемых для оплаты
                                </div>
                            </div>
                            <div class="has-feedback col-md-6">
                                <label for="sum_bo">Сумма БО</label>
                                <input type="text" name="sum_bo" class="form-control" id="sum_bo" placeholder="" value="<?=$_SESSION['form_data']['sum_bo'] ?? ($payment['sum_bo'] ?? '');?>" required <?= $type == 1 ? 'disabled' : '' ?>>
                                <div class="invalid-feedback">
                                    Введите суммы БО используемых для оплаты
                                </div>
                            </div>
                            <input type="hidden" name="id_partner" value="<?=$partner['id'] ?? '';?>">
                            <input type="hidden" name="id" value="<?=$payment['id'] ?? '';?>">
                            <input type="hidden" name="inn" value="<?=$partner['inn'] ?? '';?>">
                            <div class="form-group text-center">
                                <?php if ($type == 1) :?>
                                    <button type="button" class="btn btn-primary mt-3" onclick="history.back();">Закрыть</button>
                                <?php else :?>
                                    <?php unset($_SESSION['form_data']); ?>
                                    <button type="submit" class="btn btn-primary mt-3">Создать оплату</button>
                                <?php endif;?>
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
