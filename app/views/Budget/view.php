<main role="main" class="flex-shrink-0">
    <div class="container">
        <div class="d-flex justify-content-between">
            <h1 class="mt-1">Просмотр бюджетной операции</h1>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?=PATH;?>">Главная</a></li>
                <li class="breadcrumb-item "><a href="<?=PATH;?>/budget/upload">Загрузка данных</a></li>
                <li class="breadcrumb-item "><a href="<?=PATH;?>/budget">Бюджетные операции</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $bo['number'];?></li>
            </ol>
        </nav>
        <?php
            $date = date_create($bo['scenario']);
            $_monthsList = array(
                "1"=>"ЯНВАРЬ","2"=>"ФЕВРАЛЬ","3"=>"МАРТ",
                "4"=>"АПРЕЛЬ","5"=>"МАЙ", "6"=>"ИЮНЬ",
                "7"=>"ИЮЛЬ","8"=>"АВГУСТ","9"=>"СЕНТЯБРЬ",
                "10"=>"ОКТЯБРЬ","11"=>"НОЯБРЬ","12"=>"ДЕКАБРЬ");

            $scenario = $_monthsList[date_format($date, "n")].'&nbsp;'.date_format($date, "Y");
            $date = date_create($bo['month_exp']);
            $month_exp = $_monthsList[date_format($date, "n")];
            $date = date_create($bo['month_pay']);
            $month_pay = $_monthsList[date_format($date, "n")];
        ?>
        <div class="row alert alert-secondary my-row">
            <div class="col-10 border-right border-secondary align-middle">
                <h2 class="text-center text-primary"><b><?= $bo['number'];?></b></h2>
                <h3 class="text-center text-muted"><?= $bo['name_budget_item'];?></h3>
                <hr>
                <div class="row d-flex align-items-center">
                    <div class="col-3 text-right text-muted">Сумма БО:</div>
                    <div class="col-3 text-left"><h4><?= number_format($bo['summa'], 2, ',', '&nbsp;');?>&nbsp;₽</h4></div>
                    <div class="col-3 text-right text-muted">Остаток по БО:</div>
                    <div class="col-3 text-left text-primary"><h4><?= number_format($bo['summa'] - $bo['payment'], 2, ',', '&nbsp;');?>&nbsp;₽</h4></div>
                </div>                
            </div>
            <div class="col-2">
                <div class="text-center">
                    <small class="text-muted">месяц расхода</small>
                    <p><?= $month_exp;?></p>
                </div>
                <div class="text-center">
                    <small class="text-muted">месяц оплаты</small>
                    <p><?= $month_pay;?></p>
                </div>
            </div>
        </div>
        <?php if ($bo['payment']) : ?>
        <h2 class="text-center">Расходы по БО</h2>
        <table class="table table-striped table-sm border">
            <thead>
                <tr class="table-active text-center">
                    <th scope="col" class="h-100 align-middle">Дата</th>
                    <th scope="col" class="h-100 align-middle">Дата</th>
                    <th scope="col" class="h-100 align-middle">Контрагент</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bo['pay_arr'] as $item): ?>
                <tr>
                    <th class="text-center h-100 align-middle" scope="row"><?= $item['date_pay'];?></th>
                    <td class="text-center h-100 align-middle"><?= number_format($item['summa'], 2, ',', '&nbsp;');?>&nbsp;₽</td>
                    <th class="text-center h-100 align-middle"><a href="partner/<?= $item['partner']['inn'];?>"><?= $item['partner']['name'];?></a></th>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        <div class="d-flex justify-content-center">
            <a type="button" href="budget/edit" class="btn btn-outline-primary mt-3 edit-budget-link" data-id="<?= $bo['id'];?>" data-bs-toggle="modal" data-bs-target="#editBudgetModal">Радактировать данные БО</a>
        </div>
    </div>
</main>

<div id="editBudgetModal" class="modal fade" tabindex="-1" data-bs-backdrop="static" aria-labelledby="editBudgetModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактирование данных БО</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <form method="post" action="budget/bo-edit" id="bo-edit" role="form" class="was-validated">
                <div class="row g-3 modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
