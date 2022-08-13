<main class="flex-shrink-0">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php if (isset($_SESSION['errors'])) : ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['errors']; unset($_SESSION['errors']); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])) : ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <h1 class="mt-1">Добавление нового контрагента</h1>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?=PATH;?>">Главная</a></li>
                <li class="breadcrumb-item"><a href="<?=PATH;?>/partner">Список контрагентов</a></li>
                <li class="breadcrumb-item active" aria-current="page">Добавление нового контрагента</li>
            </ol>
        </nav>
        <div class="row d-flex justify-content-center">
            <div class="col-9">
                <form method="post" action="partner/add" id="ka_add" class="row g-3 was-validated">
                    <div class="col-12 has-feedback">
                        <label for="name">Наименование контрагента</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Наименование КА" value="<?=isset($_SESSION['form_data']['name']) ? h($_SESSION['form_data']['name']) : '';?>" required>
                    </div>
                    <div class="has-feedback col-3">
                        <label for="alias">Номер</label>
                        <input type="text" name="alias" class="form-control" id="alias" placeholder="Номер" value="<?=isset($_SESSION['form_data']['alias']) ? h($_SESSION['form_data']['alias']) : '';?>" required>
                    </div>
                    <div class="has-feedback col-3">
                        <label for="inn">ИНН</label>
                        <input type="text" name="inn" class="form-control" id="inn" placeholder="ИНН" value="<?=isset($_SESSION['form_data']['inn']) ? h($_SESSION['form_data']['inn']) : '';?>" required>
                    </div>
                    <div class="has-feedback col-3">
                        <label for="kpp">КПП</label>
                        <input type="text" name="kpp" class="form-control" id="kpp" placeholder="КПП" value="<?=isset($_SESSION['form_data']['kpp']) ? h($_SESSION['form_data']['kpp']) : '';?>">
                    </div>
                    <div class="has-feedback col-3">
                        <label for="type">Тип</label>
                        <select class="form-control" name="type" id="type">
                            <?php if (!empty($_SESSION['form_data']['type'])) : ?>
                                <?php if ($_SESSION['form_data']['type'] == 'ЮЛ') : ?>
                                    <option value="ЮЛ" selected>Юридическое лицо</option>
                                    <option value="ФЛ">Физическое лицо</option>
                                <?php elseif ($_SESSION['form_data']['type'] == 'ФЛ') : ?>
                                    <option value="ЮЛ">Юридическое лицо</option>
                                    <option value="ФЛ" selected>Физическое лицо</option>
                                <?php endif; ?>
                            <?php else : ?>
                                <option value="<?= null;?>">Выберите...</option>
                                <option value="ЮЛ">Юридическое лицо</option>
                                <option value="ФЛ">Физическое лицо</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-12 has-feedback">
                        <label for="bank">Наименование обслуживающего банка</label>
                        <input type="text" name="bank" class="form-control" id="bank" placeholder="Наименование банка" value="<?=isset($_SESSION['form_data']['bank']) ? h($_SESSION['form_data']['bank']) : null;?>">
                    </div>
                    <div class="has-feedback col-4">
                        <label for="bic">БИК</label>
                        <input type="text" name="bic" class="form-control" id="bic" placeholder="БИК" value="<?=isset($_SESSION['form_data']['bic']) ? h($_SESSION['form_data']['bic']) : null;?>">
                    </div>
                    <div class="has-feedback col-8">
                        <label for="account">Номер расчетного счета</label>
                        <input type="text" name="account" class="form-control" id="account" placeholder="Номер расчетного счета" value="<?=isset($_SESSION['form_data']['account']) ? h($_SESSION['form_data']['account']) : null;?>">
                    </div>
                    <div class="col-12 has-feedback">
                        <label for="address">Юридический адрес</label>
                        <input type="text" name="address" class="form-control" id="address" placeholder="Юридический адрес" value="<?=isset($_SESSION['form_data']['address']) ? h($_SESSION['form_data']['address']) : null;?>">
                    </div>
                    <div class="has-feedback col-3">
                        <label for="phone">Телефоны</label>
                        <input type="text" name="phone" class="form-control" id="phone" placeholder="Телефоны" value="<?=isset($_SESSION['form_data']['phone']) ? h($_SESSION['form_data']['phone']) : null;?>">
                    </div>
                    <div class="has-feedback col-3">
                        <label for="email">E-mail</label>
                        <input type="text" name="email" class="form-control" id="email" placeholder="E-mail" value="<?=isset($_SESSION['form_data']['email']) ? h($_SESSION['form_data']['email']) : null;?>">
                    </div>
                    <div class="has-feedback col-3">
                        <label for="delay">Отсрочка</label>
                        <input type="number" name="delay" class="form-control" id="delay" placeholder="Отсрочка" value="<?=isset($_SESSION['form_data']['delay']) ? h($_SESSION['form_data']['delay']) : null;?>">
                    </div>
                    <div class="has-feedback col-3">
                        <label for="vat">НДС</label>
                        <select class="form-control" name="vat" id="vat">
                            <?php if (!empty($_SESSION['form_data']['vat'])) : ?>
                                <?php if ($_SESSION['form_data']['vat'] == '1.20') : ?>
                                    <option value="1.20" selected>20%</option>
                                    <option value="1.00">Без НДС</option>
                                <?php elseif ($_SESSION['form_data']['vat'] == '1.00') : ?>
                                    <option value="1.20">20%</option>
                                    <option value="1.00" selected>Без НДС</option>
                                <?php endif; ?>
                            <?php else : ?>
                                <option value="<?= null;?>">Выберите...</option>
                                <option value="1.20">20%</option>
                                <option value="1.00">Без НДС</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-12 d-flex justify-content-center mt-3">
                        <button type="submit" class="btn btn-primary">Сохранить данные КА</button>
                    </div>
                </form>
                <?php if(isset($_SESSION['form_data'])) unset($_SESSION['form_data']); ?>
            </div>
        </div>
    </div>
</main>
