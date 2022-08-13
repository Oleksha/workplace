<div class="col-12 has-feedback">
    <label for="name">Наименование контрагента</label>
    <input type="text" name="name" class="form-control" id="name" placeholder="Наименование КА" value="<?= /** @var string $partner  Наименование КА */ $partner['name'];?>" disabled>
</div>
<div class="col-12 has-feedback">
    <label for="type">Тип документа для оплаты</label>
    <select class="form-control" name="type" id="type">
        <option value="">Выберите тип</option>
        <option value="ПТ" selected>Поступление товаров и услуг</option>
        <option value="ЗП">Заказ поставщику</option>
        <option value="АО">Авансовый отчет</option>
    </select>
</div>
<div class="has-feedback col-md-6">
    <label for="date">Дата прихода</label>
    <input type="date" name="date" class="form-control" id="date" placeholder="01.01.2021" value="<?=date("Y-m-d");?>" required>
</div>
<div class="has-feedback col-md-6">
    <label for="number">Номер прихода</label>
    <input type="text" name="number" class="form-control" id="number" placeholder="Номер" value="<?=null;?>" required>
</div>
<div class="has-feedback col-md-6">
    <label for="sum">Сумма прихода</label>
    <input type="number" name="sum" class="form-control" id="sum"  placeholder="" step="0.01" value="<?=null;?>" required>
</div>
<div class="has-feedback col-md-6">
    <label for="vat">НДС</label>
    <select class="form-control" name="vat" id="vat">
        <option value="1.20" <?php /** @var string $vat */ if ($partner['vat'] == '1.20') { echo ' selected';} ?>>20%</option>
        <option value="1.00" <?php if ($partner['vat'] == '1.00') { echo ' selected';} ?>>Без НДС</option>
    </select>
</div>
<div class="has-feedback col-md-6">
    <label for="num_doc">Номер вх.документа</label>
    <input type="text" name="num_doc" class="form-control" id="num_doc" placeholder="Номер документа" value="<?=null;?>" required>
</div>
<div class="has-feedback col-md-6">
    <label for="date_doc">Дата вх.документа</label>
    <input type="date" name="date_doc" class="form-control" id="date_doc" placeholder="" value="<?=null;?>" required>
</div>
<div class="col-12 has-feedback">
    <label for="note">Комментарий</label>
    <input type="text" name="note" class="form-control" id="note" placeholder="Комментарий" value="<?=null;?>">
</div>
<input type="hidden" name="id_partner" value="<?=$partner['id'];?>">
