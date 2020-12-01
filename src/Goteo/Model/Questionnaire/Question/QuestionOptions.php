<?php

namespace Goteo\Model\Questionnaire\Question;

use Goteo\Application\Message;
use Goteo\Application\Exception\ModelNotFoundException;

use Goteo\Application\Lang;
use Goteo\Application\Config;

class QuestionOptions extends \Goteo\Core\Model
{

    public
      $id,
      $question,
      $option,
      $lang;

    protected $Table = 'question_options';
    protected static $Table_static = 'question_options';

    public static function getLangFields() {
      return ['option'];
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
        'question',
        'option',
        'lang',
        );

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

    public static function getList ($filters = array(), $offset = 0, $limit = 0, $count = false, $lang = null) {

      $sql = "";
      $sqlWhere = [];
      $values = [];

      if ($filters['question']) {
        $sqlWhere[] = "question = :question";
        $values[":question"] = $filters["question"];
      }

      if(!$lang) $lang = Lang::current();
      list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

      if ($sqlWhere) {
        $sql = " WHERE " . implode(" AND " , $sqlWhere);
      }

      if ($count) {
          $sql = "SELECT COUNT(question_options.id)
          FROM question_options
          $joins
          $sql";
          // die(\sqldbg($sql, $values));
          return (int) self::query($sql, $values)->fetchColumn();
      }

      $sql = "SELECT 
                  question_options.id,
                  question_options.question,
                  $fields,
                  question_options.lang
              FROM question_options
              $joins
              $sql
              ORDER BY `order` ASC
              LIMIT $offset, $limit
          ";
          
      $query = static::query($sql, $values);
      // die(\sqldbg($sql, $values));
      $question_options = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
      return $question_options;
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