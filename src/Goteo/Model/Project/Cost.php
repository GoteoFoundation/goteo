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

use Goteo\Model\Project;
use Goteo\Library\Text;

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

            if($project instanceOf Project) {
                $values = array(':project' => $project->id);
                list($fields, $joins) = self::getLangsSQLJoins($lang, $project->lang);
            }
            else {
                $values = array(':project' => $project);
                list($fields, $joins) = self::getLangsSQLJoins($lang, 'project', 'project');
            }
            // die("$fields $joins");

            $sql = "
                SELECT
                    cost.id as id,
                    cost.project as project,
                    $fields,
                    cost.type as type,
                    cost.amount as amount,
                    cost.required as required,
                    cost.from as `from`,
                    cost.until as `until`
                FROM cost
                $joins
                WHERE cost.project = :project
                ORDER BY cost.required DESC, cost.order ASC, cost.id ASC
                ";
            // if($lang) die("[$lang] ".\sqldbg($sql, $values));
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

