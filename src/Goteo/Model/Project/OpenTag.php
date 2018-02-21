<?php

namespace Goteo\Model\Project;

use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Application\Exception\ModelException;

class OpenTag extends \Goteo\Core\Model {
    protected $Table = 'open_tag';
    static protected $Table_static = 'open_tag';

    public
        $id,
        $project,
        $name,
        $description;

    static public function getLangFields() {
        return ['name', 'description'];
    }

    /**
     * Get the open_tags for a project
     * @param varcahr(50) $id  Project identifier
     * @return array of categories identifiers
     */
 	public static function get ($id, $lang = null) {
        $array = array ();
        try {
            $query = static::query("SELECT open_tag FROM project_open_tag WHERE project = ?", array($id));
            $open_tags = $query->fetchAll();
            foreach ($open_tags as $cat) {
                $array[$cat[0]] = $cat[0];
            }

            return $array;
        } catch(\PDOException $e) {
			throw new ModelException($e->getMessage());
        }
	}

    /**
     * Get all open_tags available
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
                    open_tag.id as id,
                    $fields
                FROM    open_tag
                $joins
                ORDER BY name ASC ";

            // die(\sqldbg($sql));

            $query = static::query($sql);
            $open_tags = $query->fetchAll();
            foreach ($open_tags as $cat) {
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
            //Text::get('validate-open_tag-empty');

        if (empty($this->project))
            $errors[] = 'No hay ningun proyecto al que asignar';
            //Text::get('validate-open_tag-noproject');

        //cualquiera de estos errores hace fallar la validación
        if (!empty($errors))
            return false;
        else
            return true;
    }

	public function save (&$errors = array()) {
        if (!$this->validate($errors)) return false;

		try {
            $sql = "REPLACE INTO project_open_tag (project, open_tag) VALUES(:project, :open_tag)";
            $values = array(':project'=>$this->project, ':open_tag'=>$this->id);
			self::query($sql, $values);
			return true;
		} catch(\PDOException $e) {
			$errors[] = "La categoria {$open_tag} no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
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
			':open_tag'=>$this->id,
		);

		try {
            self::query("DELETE FROM project_open_tag WHERE open_tag = :open_tag AND project = :project", $values);
			return true;
		} catch(\PDOException $e) {
			$errors[] = 'No se ha podido quitar la categoria ' . $this->id . ' del proyecto ' . $this->project . ' ' . $e->getMessage();
            //Text::get('remove-open_tag-fail');
            return false;
		}
	}

}

