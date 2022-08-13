<div class="col-12 has-feedback">
    <label for="date">Дата фактической оплаты</label>
    <input type="date" name="date" class="form-control" id="date" placeholder="01.01.2021" value="<?=date("Y-m-d");?>" required>
</div>
<input type="hidden" name="id" value="<?= /** @var int $id */ $id;?>">

