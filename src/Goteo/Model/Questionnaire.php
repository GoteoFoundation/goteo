<?php

namespace Goteo\Model;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Library\Text;
use Goteo\Model\Questionnaire\Question;

class Questionnaire extends \Goteo\Core\Model
{

    public
    $id,
    $matcher,
    $lang,
    $questions;

    static public function getTypes()
    {
        return [
            'textarea' => Text::get('questionnaire-textarea'), 
            'boolean' => Text::get('questionnaire-boolean'),
            'dropfiles' => Text::get('questionnaire-dropfiles')
        ];
    }


    /**
     * Get data about a questionnaire
     *
     * @param  int $id questionnaire id.
     * @return Questionnaire object
     */
    static public function get($id)
    {
        
        $lang = Lang::current();
        // list($fields, $joins) = self::getLangsSQLJoins($lang);

        $query = static::query('SELECT * FROM questionnaire WHERE id = :id', array(':id' => $id));
        $questionnaire = $query->fetchObject(__CLASS__);

        if (!$questionnaire instanceOf Questionnaire) {
            throw new ModelNotFoundException();
        }

        $questionnaire->questions = Question::getByQuestionnaire($id);

        // $questionnaire->vars = json_decode($questionnaire->vars);
        return $questionnaire;

    }
    
    /**
     * Get data about questionnaire by matcher id
     *
     * @param  int $id matcher id.
     * @return Questionnaire object
     */
    static public function getByMatcher($mid)
    {
        
        $lang = Lang::current();
        // list($fields, $joins) = self::getLangsSQLJoins($lang);

        $query = static::query('SELECT * FROM questionnaire WHERE matcher = :id', array(':id' => $mid));
        $questionnaire = $query->fetchObject(__CLASS__);
        if ($questionnaire instanceOf Questionnaire)
            $questionnaire->questions = Question::getByQuestionnaire($questionnaire->id);

        return $questionnaire;

    }

    /**
     * Save.
     *
     * @param  type array $errors
     * @return type bool   true|false
     */
    public function save(&$errors = array())
    {
        if (!$this->validate($errors)) { return false;
        }

        $fields = array(
            'id',
            'lang',    
            'matcher'
            );

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
     * @param  type array $errors Errores devueltos pasados por referencia.
     * @return type bool   true|false
     */
    public function validate(&$errors = array())
    {
        return true;
    }


}


