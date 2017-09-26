<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

namespace Goteo\Model\Project;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Model\Project;
use Goteo\Model\Invest;
use Goteo\Model\Icon;
use Goteo\Model\Category as MainCategory;
use Goteo\Model\Image as CategoryImage;
use Goteo\Model\License;
use Goteo\Application\Lang;
use Goteo\Application\Config;

class Reward extends \Goteo\Core\Model {

    public
            $id,
            $project,
            $reward,
            $description,
            $type = 'social',
            $icon,
            $other, // para el icono de otro, texto que diga el tipo
            $license,
            $amount,
            $units;

    public static function get($id) {
        try {
            $query = static::query("SELECT * FROM reward WHERE id = :id", array(':id' => $id));
            return $query->fetchObject(__CLASS__);
        } catch (\PDOException $e) {
            throw new \Goteo\Core\Exception($e->getMessage());
        }
    }

    public static function getAll($project, $type = 'social', $lang = null, $fulfilled = null, $icon = null) {
        try {
            $array = array();

            $icons = Icon::getList();

            $values = array(
                ':project' => $project,
                ':type' => $type,
                ':lang' => $lang
            );

            $sqlFilter = "";
            if (!empty($fulfilled)) {
                $sqlFilter .= "    AND reward.fulsocial = :fulfilled";
                $values[':fulfilled'] = $fulfilled == 'ok' ? 1 : 0;
            }
            if (!empty($icon)) {
                $sqlFilter .= "    AND reward.icon = :icon";
                $values[':icon'] = $icon;
            }

            // FIXES #42
            $join = " LEFT JOIN reward_lang
                        ON  reward_lang.id = reward.id
                        AND reward_lang.project = :project
                        AND reward_lang.lang = :lang
            ";
            $eng_join = '';

            // tener en cuenta si se solicita el contenido original
            if (!isset($lang)) {
                $different_select=" reward.reward as reward,
                                    reward.description as description,
                                    reward.other as other";
                $join = '';
                unset($values[':lang']);

            } elseif(self::default_lang($lang) == Config::get('lang')) {
                $different_select=" IFNULL(reward_lang.reward, reward.reward) as reward,
                                    IFNULL(reward_lang.description, reward.description) as description,
                                    IFNULL(reward_lang.other, reward.other) as other";

            } else {
                    $different_select=" IFNULL(reward_lang.reward, IFNULL(eng.reward, reward.reward)) as reward,
                                        IFNULL(reward_lang.description, IFNULL(eng.description, reward.description)) as description,
                                        IFNULL(reward_lang.other, IFNULL(eng.other, reward.other)) as other";
                    $eng_join=" LEFT JOIN reward_lang as eng
                                    ON  eng.id = reward.id
                                    AND eng.project = :project
                                    AND eng.lang = 'en'";
                }

            $sql = "SELECT
                        reward.id as id,
                        reward.project as project,
                        {$different_select} ,
                        reward.type as type,
                        reward.icon as icon,
                        reward.license as license,
                        reward.amount as amount,
                        reward.units as units,
                        reward.fulsocial as fulsocial,
                        reward.url,
                        reward.bonus,
                        reward.category
                    FROM    reward
                    {$join}
                    {$eng_join}
                    WHERE   reward.project = :project
                        AND type= :type
                    $sqlFilter
                    ";

            if ($type == 'social') {
                $sql .= " ORDER BY reward.order ASC";
            }
            else {
                //     $sql .= " ORDER BY reward.id ASC";
                //     ORDERED BY AMOUNT
                $sql .= " ORDER BY reward.amount ASC, reward.order ASC";
            // die(\sqldbg($sql, $values));
            }
            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {

                if ($item->icon == 'other' && !empty($item->other)) {
                    $item->icon_name = $item->other;
                }
                else {
                    $item->icon_name = $icons[$item->icon]->name;
                }

                if($type == 'social'&&$item->category)
                {
                    $item->category=MainCategory::get($item->category);
                    $item->category->image=new CategoryImage($item->category->image);
                }

                $array[$item->id] = $item;
            }
            // print_r($array);
            return $array;
        } catch (\PDOException $e) {
            throw new \Goteo\Core\Exception($e->getMessage());
        }
    }

    public static function getWidget($project, $lang = null) {
        if(empty($lang)) $lang = Lang::current();
        $debug = false;

        try {
            $array = array();

            $values = array(
                ':project' => $project,
                ':lang' => $lang
            );

            $icons = Icon::getList();
            // die(\trace($icons));


            if(self::default_lang($lang)=='es') {
                $different_select=" IFNULL(reward_lang.reward, reward.reward) as reward";
                }
            else {
                    $different_select=" IFNULL(reward_lang.reward, IFNULL(eng.reward, reward.reward)) as reward";
                    $eng_join=" LEFT JOIN reward_lang as eng
                                    ON  eng.id = reward.id
                                    AND eng.project = :project
                                    AND eng.lang = 'en'";
                }

            $sql = "SELECT
                        reward.id as id,
                        reward.project as project,
                        $different_select,
                        reward.type as type,
                        reward.icon as icon,
                        reward.amount as amount
                    FROM    reward
                    LEFT JOIN reward_lang
                        ON  reward_lang.id = reward.id
                        AND reward_lang.project = :project
                        AND reward_lang.lang = :lang
                    $eng_join
                    WHERE   reward.project = :project
                    ";


            // order
            $sql .= " ORDER BY reward.order ASC, reward.id ASC";

            // limite
            $sql .= " LIMIT 4";

            if ($debug) {
                echo \trace($values);
                echo $sql;
                die;
            }


            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {

                $item->icon_name = $icons[$item->icon]->name;

                $array[$item->id] = $item;
            }
            return $array;
        } catch (\PDOException $e) {
            throw new \Goteo\Core\Exception($e->getMessage());
        }
    }

    public function validate(&$errors = array()) {
        // Estos son errores que no permiten continuar
        if (empty($this->project))
            $errors[] = 'No hay proyecto al que asignar la recompensa/rettorno';

        // hotfix
        if (empty($this->bonus))
            $this->bonus = false;

        //Text::get('validate-reward-noproject');
        /*
          if (empty($this->reward))
          $errors[] = 'No hay nombre de recompensa/retorno';
          //Text::get('validate-reward-name');

          if (empty($this->type))
          $errors[] = 'No hay tipo de recompensa/retorno';
          //Text::get('validate-reward-description');
         */
        //cualquiera de estos errores hace fallar la validación
        if (!empty($errors))
            return false;
        else
            return true;
    }

    public function save(&$errors = array()) {
        if (!$this->validate($errors))
            return false;

        $fields = array(
            // 'id',
            'project',
            'reward',
            'description',
            'type',
            'icon',
            'other',
            'license',
            'amount',
            'units',
            'bonus',
            'url',
            'category'
        );

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);
            return true;
        } catch(\PDOException $e) {
            $errors[] = "Reward save error: " . $e->getMessage();
            return false;
        }
    }

    public function saveLang(&$errors = array()) {

        $fields = array(
            'id' => 'id',
            'project'=>'project',
            'lang' => 'lang',
            'reward' => 'reward_lang',
            'description' => 'description_lang',
            'other' => 'other_lang'
        );

        $set = '';
        $values = array();

        foreach ($fields as $field => $ffield) {
            if ($set != '')
                $set .= ", ";
            $set .= "$field = :$field ";
            $values[":$field"] = $this->$ffield;
        }

        try {
            $sql = "REPLACE INTO reward_lang SET " . $set;
            self::query($sql, $values);

            return true;
        } catch (\PDOException $e) {
            $errors[] = "El retorno {$this->reward} no se ha grabado correctamente. Por favor, revise los datos." . $e->getMessage();
            return false;
        }
    }

    public function updateURL(&$errors = array()){

        $fields = array(
            'url'
        );

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Reward url save error: " . $e->getMessage();
            return false;
        }

    }


    /**
     * Quitar un retorno de un proyecto
     *
     * @param varchar(50) $project id de un proyecto
     * @param INT(12) $id  identificador de la tabla reward
     * @param array $errors
     * @return boolean
     */
    public function remove(&$errors = array()) {
        $values = array(
            ':project' => $this->project,
            ':id' => $this->id,
        );

        try {
            self::query("DELETE FROM reward WHERE id = :id AND project = :project", $values);
            return true;
        } catch (\PDOException $e) {
            $errors[] = 'No se ha podido quitar el retorno ' . $this->id . '. ' . $e->getMessage();
            //Text::get('remove-reward-fail');
            return false;
        }
    }

    /**
     * Calcula y actualiza las unidades de recompensa comprometidas por aporte
     * @param void
     * @return numeric
     */
    public function getTaken() {
        if($this->taken) return $this->taken;
        // cuantas de esta recompensa en aportes no cancelados
        $sql = "SELECT
                    COUNT(invest_reward.reward) as taken
                FROM invest_reward
                INNER JOIN invest
                    ON invest.id = invest_reward.invest
                    AND invest.status IN ('0', '1', '3', '4')
                    AND invest.project = :project
                WHERE invest_reward.reward = :reward
            ";

        $values = array(
            ':project' => $this->project,
            ':reward' => $this->id
        );

        $query = self::query($sql, $values);
        if ($this->taken = $query->fetchColumn(0)) {
            return $this->taken;
        } else {
            return 0;
        }
    }

    /**
     * Checks if this reward is available for buying
     * @return boolean true if can be used, false otherwise
     */
    public function available() {
        return (empty($this->units) || ($this->units > $this->getTaken()));
    }

    // returns the current project
    public function getProject() {
        if(isset($this->projectObject)) return $this->projectObject;
        try {
            $this->projectObject = Project::get($this->project);
        } catch(ModelNotFoundException $e) {
            $this->projectObject = false;
        }
        return $this->projectObject;
    }


    /**
     * Returns true if reward (Object or Id) has been choosen by the invest
     * @param  [type]  $reward [description]
     * @return boolean         [description]
     */
    public function inInvest($invest) {
        if(!$invest instanceOf Invest) {
            return false;
        }
        if($rewards = $invest->getRewards()) {
            foreach($rewards as $r) {
                if($r->id == $this->id) return true;
            }
        }
        return false;
    }
    public static function icons($type = 'social') {
        $list = array();

        $icons = Icon::getAll($type);

        foreach ($icons as $icon) {
            $list[$icon->id] = $icon;
        }

        return $list;
    }

    public static function licenses() {
        $list = array();

        $licenses = License::getAll();

        foreach ($licenses as $license) {
            $list[$license->id] = $license->name;
        }

        return $list;
    }

    /**
     * Para saber si ha cumplido con recompensas/retornos
     * @param string $project id de un proyecto
     * @param string $type individual|social
     * @return boolean
     */

    public static function areFulfilled($project, $type = 'individual') {

        // diferente segun tipo
        if ($type == 'social') {
            $sql = "SELECT
                        COUNT(id)
                    FROM reward
                    WHERE project = :project
                    AND type = 'social'
                    AND fulsocial != 1
                ";
        } else {
            $sql = "SELECT
                        COUNT(invest_reward.reward)
                    FROM invest_reward
                    INNER JOIN invest
                        ON invest.id = invest_reward.invest
                        AND invest.status IN ('1', '3')
                        AND invest.project = :project
                    WHERE invest_reward.fulfilled != 1
                ";
        }

        $values = array(
            ':project' => $project
        );

        $query = self::query($sql, $values);
        $fulfilled = $query->fetchColumn(0);
        if ($fulfilled == 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function getChosen($filters = array(), $offset = 0, $limit = 10, $count = false) {
        try {
            $array = array();

            $values = array();

            $sqlFilter = "";
            if (!empty($filters['project'])) {
                $sqlFilterProj = " AND project.id = :project";
                $values[':project'] = $filters['project'];
            }
            if (!empty($filters['name'])) {
                $sqlFilterUser = " AND (user.name LIKE :name OR user.email LIKE :name)";
                $values[':name'] = "%{$filters['name']}%";
            }
            $and = " WHERE";
            if (!empty($filters['status'])) {
                $sqlFilter .= $and." invest_reward.fulfilled = :status";
                $values[':status'] = $filters['status'] == 'ok' ? 1 : 0;
                $and = " AND";
            }
            if (!empty($filters['friend'])) {
                $not = ($filters['friend'] == 'only') ? '' : 'NOT';
                $sqlFilter .= $and." invest_reward.invest {$not} IN (SELECT invest FROM invest_address WHERE regalo = 1)";
                $and = " AND";
            }

            // Return total count for pagination
            if($count) {
                $sql = "SELECT COUNT(invest_reward.invest)
                    FROM invest_reward
                    INNER JOIN invest
                        ON invest.id = invest_reward.invest
                        AND invest.status IN (0, 1, 3)
                    INNER JOIN user
                        ON user.id = invest.user
                        $sqlFilterUser
                    INNER JOIN project
                        ON project.id = invest.project
                        AND project.status IN (3, 4, 5)
                        $sqlFilterProj
                    INNER JOIN reward
                        ON reward.id = invest_reward.reward
                    $sqlFilter";
                return (int) self::query($sql, $values)->fetchColumn();
            }

            $offset = (int) $offset;
            $limit = (int) $limit;
            $sql = "SELECT
                        invest_reward.invest as invest,
                        reward.reward as reward_name,
                        user.id as user,
                        user.name as name,
                        user.email as email,
                        reward.project as project,
                        invest_reward.fulfilled as fulfilled,
                        invest_reward.reward as reward,
                        invest.amount as amount
                    FROM invest_reward
                    INNER JOIN invest
                        ON invest.id = invest_reward.invest
                        AND invest.status IN (0, 1, 3)
                    INNER JOIN user
                        ON user.id = invest.user
                        $sqlFilterUser
                    INNER JOIN project
                        ON project.id = invest.project
                        AND project.status IN (3, 4, 5)
                        $sqlFilterProj
                    INNER JOIN reward
                        ON reward.id = invest_reward.reward
                    $sqlFilter
                    ";

            $sql .= " ORDER BY user.name ASC LIMIT $offset,$limit";
            // die(\sqldbg($sql, $values));
            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {

                $array[$item->invest] = $item;
            }
            return $array;
        } catch (\PDOException $e) {
            throw new \Goteo\Core\Exception($e->getMessage());
        }
    }

    /*
     * Método simple para sacar la lista de recompensas de un aporte
     */
    public static function txtRewards($invest) {
        try {
            $array = array();

            $sql = "SELECT
                        reward.reward as name
                    FROM invest_reward
                    INNER JOIN reward
                        ON reward.id = invest_reward.reward
                    WHERE invest_reward.invest = ?
                    ORDER BY reward.amount ASC
                    ";

            $query = self::query($sql, array($invest));
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $array[] = $item->name;
            }
            return implode(', ', $array);

        } catch (\PDOException $e) {
            return '';
        }
    }


}
