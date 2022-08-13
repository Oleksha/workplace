<input type="hidden" name="id" value="<?= /** @var array $budget содержит все данные о приходе */ $budget['id'];?>">
<?php
$date = date_create($budget['scenario']);
$_monthsList = array(
    "1"=>"Январь","2"=>"Февраль","3"=>"Март",
    "4"=>"Апрель","5"=>"Май", "6"=>"Июнь",
    "7"=>"Июль","8"=>"Август","9"=>"Сентябрь",
    "10"=>"Октябрь","11"=>"Ноябрь","12"=>"Декабрь");

$scenario = $_monthsList[date_format($date, "n")].'&nbsp;'.date_format($date, "Y");
?>
<div class="col-12 has-feedback">
    <label for="scenario">Сценарий</label>
    <input type="text" name="scenario" class="form-control text-center" id="scenario" value="<?=$scenario;?>" disabled>
</div>
<div class="has-feedback col-md-6">
    <label for="month_exp">Месяц расхода</label>
    <input type="date" name="month_exp" class="form-control" id="month_exp" placeholder="01.01.2021" value="<?=$budget['month_exp'];?>" required>
</div>
<div class="has-feedback col-md-6">
    <label for="month_pay">Месяц оплаты</label>
    <input type="date" name="month_pay" class="form-control" id="month_pay" placeholder="Номер" value="<?=$budget['month_pay'];?>" required>
</div>
<div class="has-feedback col-md-6">
    <label for="summa">Сумма БО</label>
    <input type="number" name="summa" class="form-control" id="summa"  placeholder="" step="0.01" value="<?=$budget['summa'];?>" required>
</div>
<div class="has-feedback col-md-6">
    <label for="vat">НДС</label>
    <select class="form-control" name="vat" id="vat">
        <option value="1.20" <?php if ($budget['vat'] == '1.20') { echo ' selected';} ?>>20%</option>
        <option value="1.00" <?php if ($budget['vat'] == '1.00') { echo ' selected';} ?>>Без НДС</option>
    </select>
</div>
<div class="col-12 has-feedback">
    <label for="budget_item">Статья расхода</label>
    <select class="form-control" name="budget_item_id" id="budget_item">
        <?php /** @var array $budget_items статьи расхода*/
        foreach ($budget_items as $item) : ?>
            <option value="<?= (int)$item['id']; ?>" <?php if ($budget['name_budget_item'] == $item['name_budget_item']) { echo ' selected';} ?>><?= $item['name_budget_item']; ?></option>
        <?php endforeach; ?>
    </select>
</div>
<input type="hidden" name="scenario" value="<?=$budget['scenario'];?>">
<input type="hidden" name="number" value="<?=$budget['number'];?>">
<input type="hidden" name="status" value="<?=$budget['status'];?>">
