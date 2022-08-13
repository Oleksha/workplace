<?php if($budgets): ?>
    <table id="bo_view" class="display" style="width:100%">
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
                $date = date_create($budget->scenario);
                $_monthsList = array(
                    "1"=>"Янв","2"=>"Фев","3"=>"Мар",
                    "4"=>"Апр","5"=>"Май", "6"=>"Июн",
                    "7"=>"Июл","8"=>"Авг","9"=>"Сен",
                    "10"=>"Окт","11"=>"Ноя","12"=>"Дек");

                $scenario = $_monthsList[date_format($date, "n")].'&nbsp;'.date_format($date, "Y");
                $date = date_create($budget->month_exp);
                $month_exp = $_monthsList[date_format($date, "n")];//.'&nbsp;'.date_format($date, "Y");
                $date = date_create($budget->month_pay);
                $month_pay = $_monthsList[date_format($date, "n")];//.'&nbsp;'.date_format($date, "Y");
                ?>
                <td><?= $scenario;?></td>
                <td><?= $month_exp;?></td>
                <td><?= $month_pay;?></td>
                <th><?= $budget->number;?></th>
                <td><?= number_format($budget->summa, 2, ',', '&nbsp;');?>&nbsp;₽</td>
                <td><?= number_format($budget->payment, 2, ',', '&nbsp;');?>&nbsp;₽</td>
                <th><?= number_format($budget->summa - $budget->payment, 2, ',', '&nbsp;');?>&nbsp;₽</th>
                <td><?= $budget->vat;?></td>
                <td><?= $budget->budget_item;?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

