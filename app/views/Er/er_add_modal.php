<?php if (!empty($_SESSION['er'])) : ?>
    <?php foreach ($_SESSION['er'] as $id => $itemer) : ?>
        <input type="hidden" name="id_partner" value="<?=isset($itemer['id_partner']) ? h($itemer['id_partner']) : '';?>">
        <div class="col-12 has-feedback">
            <label for="name">Наименование контрагента</label>
            <input type="text" name="name" class="form-control" id="name" placeholder="Наименование КА" value="<?=isset($itemer['name_partner']) ? h($itemer['name_partner']) : '';?>" disabled>
        </div>
        <div class="col-12 has-feedback">
            <label for="number">Номер</label>
            <input type="text" name="number" class="form-control" id="number" placeholder="Номер ЕР" value="<?=isset($itemer['number']) ? h($itemer['number']) : '';?>" required>
        </div>
        <div class="col-12 has-feedback">
            <label for="id_budget_item">Статья расхода</label>
            <select name="id_budget_item" class="form-control" id="id_budget_itemdata_start" required>
                <?php foreach ($itemer['budget_items'] as $item) : ?>
                    <option value="<?=$item['id']; ?>"
                        <?php if ($item['id'] == $itemer['id_budget_item']) : ?>
                            selected
                        <?php endif; ?>
                    ><?=$item['name_budget_item']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group has-feedback col-md-6">
            <label for="data_start" class="control-label">Дата начала действия</label>
            <input type="date" name="data_start" class="form-control" id="data_start" placeholder="" value="<?=isset($itemer['data_start']) ? h($itemer['data_start']) : '';?>" required>
        </div>
        <div class="form-group has-feedback col-md-6">
            <label for="data_end" class="control-label">Дата окончания действия</label>
            <input type="date" name="data_end" class="form-control" id="data_end" placeholder="" value="<?=isset($itemer['data_end']) ? h($itemer['data_end']) : '';?>" required>
        </div>
        <div class="form-group has-feedback col-md-6">
            <label for="otsrochka" class="control-label">Осрочка платежа в календарных днях</label>
            <input type="number" name="otsrochka" class="form-control" id="otsrochka" placeholder="" value="<?=isset($itemer['otsrochka']) ? h($itemer['otsrochka']) : '';?>" required>
        </div>
        <div class="form-group has-feedback col-md-6">
            <label for="summa" class="control-label">Сумма единоличного решения</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">₽</div>
                </div>
                <input type="number" name="summa" class="form-control" id="summa" placeholder="" step="0.01" value="<?=isset($itemer['summa']) ? h($itemer['summa']) : '';?>" required>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
