<?php

namespace Goteo\Model\Questionnaire;

class Score extends \Goteo\Core\Model
{

    public
      $id,
      $question,
      $answer,
      $evaluator,
      $score;
    
    protected $Table = 'question_score';
    static protected $Table_static = 'question_score';

    public function validate(&$errors = array())
    {
      return true;
    }

    /**
     * Lists of scored answers
     * @param  array   $filters [description]
     * @param  [type]  $offset  [description]
     * @param  integer $limit   [description]
     * @param  boolean $count   [description]
     * @return array
     */
    static public function getList($filters = [], $offset = 0, $limit = 10, $count = false, $lang = null) {
      
      $filter = [];
      $values = [];

      foreach(['id', 'evaluator', 'question', 'answer'] as $key) {
        if (isset($filters[$key])) {
            $filter[] = "question_score.$key LIKE :$key";
            $values[":$key"] = '%'.$filters[$key].'%';
        }
      }

      if ($filters['answers']) {
        $filter[] = "question_score.answer IN (:answer)";
        $values[":answer"] = implode(',', $filters['answers']);
      }

      if($filter) {
        $sql = " WHERE " . implode(' AND ', $filter);
      }

      if($count) {
        $sql = "SELECT COUNT(id) FROM question_score $sql";
        return (int) self::query($sql, $values)->fetchColumn();
      }

      $sql = "SELECT *
              FROM question_score
              $sql 
              ";

      $offset = (int) $offset;
      $limit = (int) $limit;
      
      if ($limit) 
              $sql .= "LIMIT $offset,$limit";

      if($query = self::query($sql, $values)) {
          return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
      }
      return [];
    }

    static public function getByAnswer($answer) {
      $query = static::query('SELECT * 
            FROM question_score 
            WHERE answer = :answer', array(':answer' => $answer));
      $score = $query->fetchObject(__CLASS__);

      return $score;
    }

    static public function getScoreByAnswers($answers = []) {
      $query = static::query('SELECT sum(question_score.score)
                            FROM question_score
                            WHERE question_score.answer IN (:answers)', [":answers" => implode(",", $answers)]);
      $score = $query->fetchColumn();
      return $score;
    }
    
    public function save(&$errors = array())
    {
      if(!$this->validate($errors)) return false;

      $fields = array(
          'id',
          'question',
          'answer',
          'evaluator',
          'score'
      );

      try {
          $this->dbInsertUpdate($fields);

        } catch(\PDOException $e) {
          print_r($e->getMessage()); die;
          $errors[] = $e->getMessage();
          return false;
      }

      return true;
    }

}