<?php

namespace Goteo\Model\Questionnaire\Answer;

use Goteo\Application\Message;
use Goteo\Application\Exception\ModelNotFoundException;

use Goteo\Application\Lang;
use Goteo\Application\Config;

class AnswerOptions extends \Goteo\Core\Model
{

    public
      $answer,
      $option,
      $other,
      $order = 1;

    protected $Table = "question_answer_options";
    
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
          'answer',
          'option',
          'other',
          'order'
        );

        $values = array(
          ':answer' => $this->answer,
          ':option' => $this->option,
          ':other' => $this->other,
          ':order' => $this->order
        );

        $sql = "REPLACE INTO `question_answer_options` (`answer`, `option`, `other`, `order`)
             VALUES (:answer, :option, :other, :order)";
        try {
            // die(\sqldbg($sql, $values));
            self::query($sql, $values);
        } catch (\PDOException $e) {
            $errors[] = "ERROR creating answer options";
            return false;
        }
    }

    public static function getList ($filters = array(), $offset = 0, $limit = 10, $count = false, $lang = null) {

      $sql = "";
      $sqlWhere = [];
      $values = [];

      if ($filters['answer']) {
        $sqlWhere[] = "answer = :answer";
        $values[":answer"] = $filters["answer"];
      }

      if ($sqlWhere) {
        $sql = " WHERE " . implode(" AND " , $sqlWhere);
      }

      if ($count) {
          $sql = "SELECT COUNT(question_answer_options.answer)
          FROM question_answer_options
          $joins
          $sql";
          // die(\sqldbg($sql, $values));
          return (int) self::query($sql, $values)->fetchColumn();
      }

      $sql = "SELECT 
                  question_answer_options.answer,
                  question_answer_options.option,
                  question_answer_options.other,
                  question_answer_options.order
              FROM question_answer_options
              $sql
              ORDER BY `order` ASC
              LIMIT $offset, $limit
          ";
          
      $query = static::query($sql, $values);
      // die(\sqldbg($sql, $values));
      $question_answer_options = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
      return $question_answer_options;
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