<?php

namespace Goteo\Model\Questionnaire;

use Goteo\Application\Message;

class Question extends \Goteo\Core\Model
{

    public
    $id,
    $title,
    $questionnaire,
    $lang,
    $vars;

    static public function getTypes()
    {
        return [
        'textarea' => Text::get('questionnaire-textarea'), 
        'boolean' => Text::get('questionnaire-boolean'),
        'dropfiles' => Text::get('questionnaire-dropfiles')
        ];
    }

    static public function get($id)
    {
        // $lang = Lang::current();
        // list($fields, $joins) = self::getLangsSQLJoins($lang);

        $query = static::query('SELECT * FROM question WHERE id = :id', array(':id' => $id));
        $question = $query->fetchObject(__CLASS__);

        if (!$question instanceOf Question) {
            throw new ModelNotFoundException();
        }

        $question->vars = json_decode($question->vars);
        return $question;

    }

    static public function getByQuestionnaire($qid)
    {
        // $lang == Lang::current();

        $query = static::query('SELECT * FROM question WHERE questionnaire = :qid', array(':qid' => $qid));
        $questions = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        foreach((array)$questions as $question) {
            $question->vars = json_decode($question->vars);
        }
        // $questions->vars = json_decode($questions->vars);
        return $questions;
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
        'title',
        'questionnaire',
        'lang',
        'vars'
        );

        $this->vars = json_encode($this->vars);

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);
            return true;
        } catch(\PDOException $e) {
            $errors[] = "Save error " . $e->getMessage();
            Message::error($e->getMessage());
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