<?php if ($payments) : ?>
    <table class="table table-striped table-sm">
        <thead>
            <tr class="table-active text-center">
                <td class="h-100 align-middle">Сумма ЕР</td>
                <th scope="col" class="h-100 align-middle"><?= number_format($er['summa'], 2, ',', '&nbsp;');?>&nbsp;₽</th>
                <th scope="col" class="h-100 align-middle"></th>
            </tr>
            <tr class="table-active text-center">
                <th scope="col" class="h-100 align-middle">Дата расхода</th>
                <th scope="col" class="h-100 align-middle">Сумма</th>
                <th scope="col" class="h-100 align-middle">Оплата</th>
            </tr>
        </thead>
        <tbody>
            <?php $sum = 0.00; ?>
            <?php foreach ($payments as $payment) : ?>
            <?php
                $nums = explode(';', trim($payment->num_er));
                $sums = explode(';', trim($payment->sum_er));
                $key = array_search($er->number, $nums);
                $sum += $sums[$key];
                $vat = $payment->vat;
            ?>
            <tr>
                <td class="text-center h-100 align-middle"><?= $payment['date_pay'];?></td>
                <td class="text-center h-100 align-middle"><?= number_format($sums[$key], 2, ',', '&nbsp;');?>&nbsp;₽</td>
                <td></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr class="table-active text-center">
            <th scope="col" class="h-100 align-middle">Итого по оплатам</th>
            <th scope="col" class="h-100 align-middle"><?= number_format($sum, 2, ',', '&nbsp;');?>&nbsp;₽</th>
            <th scope="col" class="h-100 align-middle"></th>
        </tr>
        <tr class="table-active text-center">
            <th scope="col" class="h-100 align-middle">Итого по ЕР без НДС</th>
            <th scope="col" class="h-100 align-middle"><?= number_format($sum/$vat, 2, ',', '&nbsp;');?>&nbsp;₽</th>
            <th scope="col" class="h-100 align-middle"></th>
        </tr>
        <tr class="table-active text-center">
            <td class="h-100 align-middle">остаток по ЕР</td>
            <th scope="col" class="h-100 align-middle"><?= number_format($er['summa'] - $sum/$vat, 2, ',', '&nbsp;');?>&nbsp;₽</th>
            <th scope="col" class="h-100 align-middle"></th>
        </tr>
        </tfoot>
    </table>

<?php endif; ?>
