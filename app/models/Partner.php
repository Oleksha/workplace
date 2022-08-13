<?php

namespace app\models;

use R;

class Partner extends AppModel {

    public $attributes = [
        'name' => '',
        'alias' => '',        
        'type' => '',
        'inn' => null,
        'kpp' => null,
        'bank' => null,
        'bic' => null,
        'account' => null,
        'address' => null,
        'phone' => null,
        'email' => null,
        'delay' => null,
        'vat' => null,
    ];

    /**
     * Проверяет наличие имеющихся КА с такими inn или alias
     * @return bool TRUE если inn и alias свободны, и FALSE если заняты
     */
    public function checkUnique(): bool {
        // попытаемся найти в БД пользователя с таким inn или alias
        $ka = R::getRow("SELECT * FROM partner WHERE inn = ? OR alias = ?", [$this->attributes['inn'], $this->attributes['alias']]);
        if ($ka) {
            // если нашли такую запись
            if ($ka['inn'] == $this->attributes['inn']) {
                // совпадает inn
                $this->errors['unique'][] = "C таким ИНН ({$ka['inn']}) в БД существует КА ({$ka['name']})...";
            }
            if ($ka['alias'] == $this->attributes['alias']) {
                // совпадает alias
                $this->errors['unique'][] = "C таким номером ({$ka['alias']}) в БД существует КА ({$ka['name']})...";
            }
            return false;
        }
        return true;
    }

    /**
     * Возвращает массив данных о всех КА
     * @param $sort_name string поле если нужна сортировка
     * @return array|false
     */
    public function getPartner($sort_name = false) {
        if ($sort_name) {
            $sql = "SELECT * FROM partner ORDER BY $sort_name";
        } else {
            $sql = "SELECT * FROM partner";
        }
        $partner = R::getAssocRow($sql);
        if (!empty($partner)) return $partner;
        return false;
    }

    /**
     * Возвращает массив данных о КА
     * @param $name string наименование КА
     * @return array|false
     */
    public function getPartnerByName(string $name) {
        $partner = R::getAssocRow('SELECT * FROM partner WHERE name = ? LIMIT 1', [$name]);
        if (!empty($partner)) return $partner[0];
        return false;
    }

    /**
     * Возвращает массив данных о КА
     * @param $inn string ИНН КА
     * @return array|false
     */
    public function getPartnerByINN(string $inn) {
        $partner = R::getAssocRow('SELECT * FROM partner WHERE inn = ? LIMIT 1', [$inn]);
        if (!empty($partner)) return $partner[0];
        return false;
    }

    /**
     * Возвращает массив данных о КА
     * @param $id string идентификатор КА
     * @return array|false
     */
    public function getPartnerByID(string $id) {
        $partner = R::getAssocRow('SELECT * FROM partner WHERE id = ? LIMIT 1', [$id]);
        if (!empty($partner)) return $partner[0];
        return false;
    }

}
