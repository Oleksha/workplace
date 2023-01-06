<?php if (!empty($partner)) : ?>
    <input type="hidden" name="id_partner" value="<?=isset($partner['id']) ? h($partner['id']) : '';?>">
    <div class="col-12 has-feedback">
        <label for="name">Наименование контрагента</label>
        <input type="text" name="name" class="form-control" id="name" placeholder="Наименование КА" value="<?=isset($partner['name']) ? h($partner['name']) : '';?>" disabled>
    </div>
    <div class="col-12 has-feedback">
        <label for="number">Номер</label>
        <input type="text" name="number" class="form-control" id="number" placeholder="Номер ЕР" required>
    </div>
    <div class="col-12 has-feedback">
        <label for="id_budget_item">Статья расхода</label>
        <select name="id_budget_item" class="form-control" id="id_budget_item" required>
            <?php foreach ($budget as $item) : ?>
                <option value="<?=$item['id']; ?>"><?=$item['name_budget_item']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group has-feedback col-md-6">
        <label for="data_start" class="control-label">Дата начала действия</label>
        <input type="date" name="data_start" class="form-control" id="data_start" required>
    </div>
    <div class="form-group has-feedback col-md-6">
        <label for="data_end" class="control-label">Дата окончания действия</label>
        <input type="date" name="data_end" class="form-control" id="data_end" required>
    </div>
    <div class="form-group has-feedback col-md-6">
        <label for="delay" class="control-label">Осрочка платежа в календарных днях</label>
        <input type="number" name="delay" class="form-control" id="delay" required>
    </div>
    <div class="form-group has-feedback col-md-6">
        <label for="summa" class="control-label">Сумма единоличного решения</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">₽</div>
            </div>
            <input type="number" name="summa" class="form-control" id="summa" placeholder="" step="0.01" required>
        </div>
    </div>
<?php endif; ?>
