<main role="main" class="flex-shrink-0">
    <div class="container">
        <div class="d-flex justify-content-between">
            <h1 class="mt-1">Список бюджетных операций</h1>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?=PATH;?>">Главная</a></li>
                <li class="breadcrumb-item "><a href="<?=PATH;?>/budget/upload">Загрузка данных</a></li>
                <li class="breadcrumb-item active" aria-current="page">Бюджетные операции</li>
            </ol>
        </nav>

        <div class="breadcrumb filters">
            <div class="col-auto me-3">
                <div class="input-group">
                    <label class="input-group-text" for="select_year">Год</label>
                    <select class="form-select" id="select_year">
                        <option value="2021" <?php /** @var string $year */
                        if ($year == '2021') echo ' selected'; ?>>2021</option>
                        <option value="2022" <?php if ($year == '2022') echo ' selected'; ?>>2022</option>
                    </select>
                </div>
            </div>
            <div class="col-auto">
                <div class="input-group">
                    <label class="input-group-text" for="select_month">Месяц</label>
                    <select class="form-select" id="select_month">
                        <option value="01" <?php /** @var string $month */
                        if ($month == '01') echo ' selected'; ?>>Январь</option>
                        <option value="02" <?php if ($month == '02') echo ' selected'; ?>>Февраль</option>
                        <option value="03" <?php if ($month == '03') echo ' selected'; ?>>Март</option>
                        <option value="04" <?php if ($month == '04') echo ' selected'; ?>>Апрель</option>
                        <option value="05" <?php if ($month == '05') echo ' selected'; ?>>Май</option>
                        <option value="06" <?php if ($month == '06') echo ' selected'; ?>>Июнь</option>
                        <option value="07" <?php if ($month == '07') echo ' selected'; ?>>Июль</option>
                        <option value="08" <?php if ($month == '08') echo ' selected'; ?>>Август</option>
                        <option value="09" <?php if ($month == '09') echo ' selected'; ?>>Сентябрь</option>
                        <option value="10" <?php if ($month == '10') echo ' selected'; ?>>Октябрь</option>
                        <option value="11" <?php if ($month == '11') echo ' selected'; ?>>Ноябрь</option>
                        <option value="12" <?php if ($month == '12') echo ' selected'; ?>>Декабрь</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="product-one">
            <?php /** @var array $budgets */
            if($budgets): ?>
                <table id="bo_view" class="table display" style="width:100%">
                    <thead>
                    <tr>
                        <th>Сценарий</th>
                        <th>МР</th>
                        <th>МО</th>
                        <th>Номер</th>
                        <th>Сумма</th>
                        <th>Оплачено</th>
                        <th>Остаток</th>
                        <th>НДС</th>
                        <th>Статья</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($budgets as $budget): ?>
                        <tr>
                            <?php
                            $date = date_create($budget['scenario']);
                            $_monthsList = array(
                                "1"=>"Янв","2"=>"Фев","3"=>"Мар",
                                "4"=>"Апр","5"=>"Май", "6"=>"Июн",
                                "7"=>"Июл","8"=>"Авг","9"=>"Сен",
                                "10"=>"Окт","11"=>"Ноя","12"=>"Дек");

                            $scenario = $_monthsList[date_format($date, "n")].'&nbsp;'.date_format($date, "Y");
                            $date = date_create($budget['month_exp']);
                            $month_exp = $_monthsList[date_format($date, "n")];//.'&nbsp;'.date_format($date, "Y");
                            $date = date_create($budget['month_pay']);
                            $month_pay = $_monthsList[date_format($date, "n")];//.'&nbsp;'.date_format($date, "Y");
                            ?>
                            <td><?= $scenario;?></td>
                            <td><?= $month_exp;?></td>
                            <td><?= $month_pay;?></td>
                            <th><a href="budget/view?id=<?= $budget->id;?>"><?= $budget->number;?></a></th>
                            <td><?= number_format($budget->summa, 2, ',', '&nbsp;');?>&nbsp;₽</td>
                            <td><?= number_format($budget->payment, 2, ',', '&nbsp;');?>&nbsp;₽</td>
                            <th><?= number_format($budget->summa - $budget->payment, 2, ',', '&nbsp;');?>&nbsp;₽</th>
                            <td><?= $budget->vat;?></td>
                            <td><?= $budget->budget_item_name;?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</main>
<script type="text/javascript" src="assets/DataTables/datatables.min.js"></script>
<script>
    $(function () {
        $('#bo_view').dataTable( {
            "ordering": false,
            "language": {
                "url": "/assets/DataTables/ru.json"
            }
        });
    });
</script>
