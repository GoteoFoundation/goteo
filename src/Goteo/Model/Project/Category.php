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

use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Application\Exception\ModelException;
// use Goteo\Model\Image;

class Category extends \Goteo\Core\Model {

    public
        $id,
        $project,
        $name,
        $description;


    static public function getLangFields() {
        return ['name', 'description'];
    }


    /**
     * Get the categories for a project
     * @param varcahr(50) $id  Project identifier
     * @return array of categories identifiers
     */
 	public static function get ($id) {
        $array = array ();
        try {
            $query = static::query("SELECT category FROM project_category WHERE project = ?", array($id));
            $categories = $query->fetchAll();
            foreach ($categories as $cat) {
                $array[$cat[0]] = $cat[0];
            }

            return $array;
        } catch(\PDOException $e) {
			throw new ModelException($e->getMessage());
        }
	}

    /**
     * Get all categories available
     *
     * @param void
     * @return array
     */
	public static function getAll ($lang = null) {
        if(!$lang) $lang = Lang::current();
        $array = array ();
        try {

            list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

            $sql="SELECT
                    category.id as id,
                    $fields
                FROM    category
                $joins
                ORDER BY name ASC
                    ";

            // die(\sqldbg($sql));
            $query = static::query($sql);


            $categories = $query->fetchAll();
            foreach ($categories as $cat) {
            // la 15 es de testeos
            if ($cat[0] == 15) continue;
                $array[$cat[0]] = $cat[1];
            }

            return $array;


        } catch(\PDOException $e) {
			throw new ModelException($e->getMessage());
        }
	}

    /**
     * Get all categories for this project by name
     *
     * @param void
     * @return array
     */
	public static function getNames ($project = null, $limit = null, $lang = null) {
        if(!$lang) $lang = Lang::current();
        $array = array ();
        try {
            $sqlFilter = "";
            if (!empty($project)) {
                $sqlFilter = " WHERE category.id IN (SELECT category FROM project_category WHERE project = '$project')";
            }

            list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

            $sql="SELECT
                        category.id,
                        $fields
                    FROM category
                    $joins
                    $sqlFilter
                    ORDER BY `order` ASC
                    ";


            if (!empty($limit)) {
                $sql .= " LIMIT $limit";
            }
            $query = static::query($sql);
            $categories = $query->fetchAll();
            foreach ($categories as $cat) {
                // la 15 es de testeos
                if ($cat[0] == 15) continue;
                $array[$cat[0]] = $cat[1];
            }

            return $array;
        } catch(\PDOException $e) {
			throw new ModelException($e->getMessage());
        }
	}

	public function validate(&$errors = array()) {
        // Estos son errores que no permiten continuar
        if (empty($this->id))
            $errors[] = 'No hay ninguna categoria para guardar';
            //Text::get('validate-category-empty');

        if (empty($this->project))
            $errors[] = 'No hay ningun proyecto al que asignar';
            //Text::get('validate-category-noproject');

        //cualquiera de estos errores hace fallar la validación
        if (!empty($errors))
            return false;
        else
            return true;
    }

	public function save (&$errors = array()) {
        if (!$this->validate($errors)) return false;

		try {
            $sql = "REPLACE INTO project_category (project, category) VALUES(:project, :category)";
            $values = array(':project'=>$this->project, ':category'=>$this->id);
			self::query($sql, $values);
			return true;
		} catch(\PDOException $e) {
			$errors[] = "La categoria {$category} no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
            return false;
		}

	}

	/**
	 * Quitar una palabra clave de un proyecto
	 *
	 * @param varchar(50) $project id de un proyecto
	 * @param INT(12) $id  identificador de la tabla keyword
	 * @param array $errors
	 * @return boolean
	 */
	public function remove (&$errors = array()) {
		$values = array (
			':project'=>$this->project,
			':category'=>$this->id,
		);

		try {
            self::query("DELETE FROM project_category WHERE category = :category AND project = :project", $values);
			return true;
		} catch(\PDOException $e) {
			$errors[] = 'No se ha podido quitar la categoria ' . $this->id . ' del proyecto ' . $this->project . ' ' . $e->getMessage();
            //Text::get('remove-category-fail');
            return false;
		}
	}

}

