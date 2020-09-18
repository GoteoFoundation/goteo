<?php

namespace Goteo\Model\Questionnaire;

use Goteo\Application\Message;
use Goteo\Application\Exception\ModelNotFoundException;

class Question extends \Goteo\Core\Model
{

    public
    $id,
    $title,
    $questionnaire,
    $lang,
    $order,
    $max_score = 0,
    $vars;

    static public function getTypes()
    {
        return [
        'textarea' => Text::get('questionnaire-textarea'), 
        'boolean' => Text::get('questionnaire-boolean'),
        'dropfiles' => Text::get('questionnaire-dropfiles')
        ];
    }

    public static function getLangFields() {
        return ['title'];
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

        $query = static::query('
            SELECT *
            FROM question
            WHERE questionnaire = :qid
            ORDER BY `order`', array(':qid' => $qid));
        $questions = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        foreach((array)$questions as $question) {
            $question->vars = json_decode($question->vars);
        }
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
        'order',
        'max_score',
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

    public static function getList ($filters = array(), $offset = 0, $limit = 0, $count = false) {
        $sqlWhere = "";
        $values = [];

        if ($filters['qid']) {
            $sqlWhere .= "questionnaire = :qid";
            $values[":qid"] = $filters["qid"];
        }

        if ($count) {
            $sql = "SELECT COUNT(question.id)
            FROM question
            $sqlWhere";
            return (int) self::query($sql)->fetchColumn();
        }

        $sql = "SELECT * 
                FROM question
                WHERE
                $sqlWhere
                ORDER BY `order` ASC
                LIMIT $offset, $limit
            ";
            
        $query = static::query($sql, $values);
        $questions = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        return $questions;
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