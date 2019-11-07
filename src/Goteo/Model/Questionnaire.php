<?php

namespace Goteo\Model;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Application\Config;

class Questionnaire extends \Goteo\Core\Model {

    public
    $id,
    $matcher,
    $vars,
    $description;


    /**
     * Get data about a questionnaire
     *
     * @param   int    $id questionnaire id.
     * @return  Questionnaire object
     */
    static public function get($id) {
        if (empty($id)) {
			return false;
        }
        
        $lang = Lang::current();
        // list($fields, $joins) = self::getLangsSQLJoins($lang);

		$query = static::query('SELECT * FROM questionnaire WHERE id = :id', array(':id' => $id));
        $questionnaire = $query->fetchObject(__CLASS__);
        $questionnaire->vars = json_decode($questionnaire->vars);
        return $questionnaire;

    }
    
    /**
     * Get data about questionnaire by matcher id
     *
     * @param   int    $id matcher id.
     * @return  Questionnaire object
     */
    static public function getByMatcher($id) {
        if (empty($id)) {
			return false;
        }
        
        $lang = Lang::current();
        // list($fields, $joins) = self::getLangsSQLJoins($lang);

		$query = static::query('SELECT * FROM questionnaire WHERE matcher = :id', array(':id' => $id));
        $questionnaire = $query->fetchObject(__CLASS__);
        // print_r($questionnaire->vars); die;
        $questionnaire->vars = json_decode($questionnaire->vars);
        return $questionnaire;

    }

    /**
     * Save.
     *
     * @param   type array  $errors
     * @return  type bool   true|false
     */
    public function save(&$errors = array()) {
        if (!$this->validate($errors)) return false;

        $fields = array(
            'id',
            'matcher',
            'vars'
            );

        $this->vars = json_encode($this->vars);
        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);
            return true;
        } catch(\PDOException $e) {
            $errors[] = "Save error " . $e->getMessage();
            return false;
        }
    }

    /**
     * Validate.
     *
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
    public function validate(&$errors = array()) {
        return true;
    }


}


