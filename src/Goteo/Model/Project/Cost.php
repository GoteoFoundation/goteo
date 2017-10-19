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

use Goteo\Library\Text;
use Goteo\Application\Lang;

class Cost extends \Goteo\Core\Model {

    public
        $id,
        $project,
        $cost,
		$description,
        $type = 'task',
        $amount,
        $required = true,
        $from,
		$until;

    public static function getLangFields() {
        return ['cost', 'description'];
    }

    public function setLang($lang, $data = [], array &$errors = []) {
        $data['project'] = $this->project;
        return parent::setLang($lang, $data, $errors);
    }

 	public static function get ($id) {
        try {
            $query = static::query("SELECT * FROM cost WHERE id = :id", array(':id' => $id));
            return $query->fetchObject(__CLASS__);
        } catch(\PDOException $e) {
            throw new \Goteo\Core\Exception($e->getMessage());
        }
	}

	public static function getAll ($project, $lang = null) {
        try {
            $array = array();

            // FIXES #42
            $values = array(':project'=>$project, ':lang'=>$lang);

            $join = " LEFT JOIN cost_lang
                    ON  cost_lang.id = cost.id
                    AND cost_lang.project = :project
                    AND cost_lang.lang = :lang
            ";
            $eng_join = '';

            // tener en cuenta si se solicita el contenido original
            if (!isset($lang)) {
                $different_select=" cost.cost as cost,
                                    cost.description as description";
                $join = '';
                unset($values[':lang']);

            } elseif(self::default_lang($lang)=='es') {
                $different_select=" IFNULL(cost_lang.cost, cost.cost) as cost,
                    				IFNULL(cost_lang.description, cost.description) as description";

            } else {
                $different_select=" IFNULL(cost_lang.cost, IFNULL(eng.cost, cost.cost)) as cost,
                                    IFNULL(cost_lang.description, IFNULL(eng.description, cost.description)) as description";
                $eng_join=" LEFT JOIN cost_lang as eng
                                ON  eng.id = cost.id
                                AND eng.project = :project
                                AND eng.lang = 'en'
                                ";
            }

            $sql = "
                SELECT
                    cost.id as id,
                    cost.project as project,
                    {$different_select} ,
                    cost.type as type,
                    cost.amount as amount,
                    cost.required as required,
                    cost.from as `from`,
                    cost.until as `until`
                FROM cost
                {$join}
                {$eng_join}
                WHERE cost.project = :project
                ORDER BY cost.required DESC, cost.order ASC, cost.id ASC
                ";
            // die(\sqldbg($sql, $values));
			$query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
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
            $errors[] = 'No hay proyecto al que asignar el coste';

        /*
         *  Ya no existe el concepto fechas de costes ni calendario (Julián 21/01/2015 Hotfix)
         *
        // limite de fechas

        // verificamos que tenemos fecha de creación del proyecto
        if (empty($this->project_date) || $this->project_date == '0000-00-00')
            $this->project_date = date('Y-m-d');

        $dateMin = new \DateTime($this->project_date);  // entre fecha creacion del proyecto
        // echo \trace($dateMin);

        $dateBase = new \DateTime($this->project_date);
        $dateMax = $dateBase->add(new \DateInterval('P2Y')); // y dos años después
        // echo \trace($dateMax);

        if (empty($this->from) || $this->from == '0000-00-00') {
            $this->from = date('Y-m-d');
        } else {
            //  tiene que estar dentro del limite de fechas
            $dateFrom = new \DateTime($this->from);
            if ($dateFrom > $dateMax)
                $this->from = $dateMax->format('Y-m-d');

            if ($dateFrom < $dateMin)
                $this->from = $dateMin->format('Y-m-d');

        }

        if (empty($this->until) || $this->until == '0000-00-00') {
            $this->until = date('Y-m-d');
        } else {
            //  tiene que estar dentro del limite de fechas
            $dateUntil = new \DateTime($this->until);
            if ($dateUntil > $dateMax)
                $this->until = $dateMax->format('Y-m-d');

            if ($dateUntil < $dateMin)
                $this->until = $dateMin->format('Y-m-d');

        }
        */

        //cualquiera de estos errores hace fallar la validación
        if (!empty($errors))
            return false;
        else
            return true;
    }

	public function save (&$errors = array()) {

        if (!$this->validate($errors)) {
            return false;
        }

		$fields = array(
			// 'id',
			'project',
			'cost',
			'description',
			'type',
			'amount',
			'required'
			);

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);

        	//aqui actualizar los costes en la tabla project
        	\Goteo\Model\Project::calcCosts($this->project);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Support save error: " . $e->getMessage();
            return false;
        }
	}

	public function saveLang (&$errors = array()) {

        $fields = ['id'=>'id', 'project' => 'project', 'lang'=>'lang'];
        foreach(self::getLangFields() as $key) {
            $fields[$key] = $key . '_lang';
        }

		$set = '';
		$values = array();

		foreach ($fields as $field=>$ffield) {
			if ($set != '') $set .= ", ";
			$set .= "`$field` = :$field ";
			$values[":$field"] = $this->$ffield;
		}

		try {
			$sql = "REPLACE INTO cost_lang SET " . $set;
			self::query($sql, $values);

			return true;
		} catch(\PDOException $e) {
            $errors[] = "El coste {$this->cost} no se ha grabado correctamente. Por favor, revise los datos." . $e->getMessage();
            return false;
		}
	}

	/**
	 * Quitar un coste de un proyecto
	 *
	 * @param varchar(50) $project id de un proyecto
	 * @param INT(12) $id  identificador de la tabla cost
	 * @param array $errors
	 * @return boolean
	 */
	public function remove (&$errors = array()) {
		$values = array (
			':project'=>$this->project,
			':id'=>$this->id,
		);

        try {
            self::query("DELETE FROM cost WHERE id = :id AND project = :project", $values);
            //aqui actualizar los costes en la tabla project
            \Goteo\Model\Project::calcCosts($this->project);
			return true;
		} catch (\PDOException $e) {
            $errors[] = 'No se ha podido quitar el coste del proyecto ' . $this->project . ' ' . $e->getMessage();
            //Text::get('remove-cost-fail');
            return false;
		}
	}

	public static function types() {
		return array (
			'task'=>Text::get('cost-type-task'),
			'structure'=>Text::get('cost-type-structure'),
			'material'=>Text::get('cost-type-material')
		);
	}

}

