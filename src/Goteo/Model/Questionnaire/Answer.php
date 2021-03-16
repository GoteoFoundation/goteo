<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\Questionnaire;

use Goteo\Application\Message;

class Answer extends \Goteo\Core\Model
{

    public
      $id,
      $question,
      $answer;

    protected $Table = 'question_answer';

    public function validate(&$errors = array())
    {
        return true;
    }

    public function save(&$errors = array())
    {
        if (!$this->validate($errors)) { return false;
        }

        $fields = array(
            'question',
            'answer'
            );

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);

            if ($this->project) {
                $sql = "REPLACE INTO question_answer_project VALUES(:answer, :project)";
                $values = [":answer" => $this->id, ":project" => $this->project];
                
                // die(\sqldbg($sql, $values));
                static::query($sql, $values);
            }


            return true;
        } catch(\PDOException $e) {
            Message::error($e->getMessage());
            $errors[] = "Save error " . $e->getMessage();
            return false;
        }

    }

    /**
     * Lists answers
     * @param  array   $filters [description]
     * @param  [type]  $offset  [description]
     * @param  integer $limit   [description]
     * @param  boolean $count   [description]
     * @param  string  $lang    [description]
     * @return array[Answer]
     */
    static public function getList($filters = [], $offset = 0, $limit = 10, $count = false, $lang = null) {
        $values = [];
        $filter = [];
        $sqlInner = "";

        if ($filters['questionnaire']) {
            $sqlInner .= "LEFT JOIN question
            ON question.questionnaire = :questionnaire AND question_answer.question = question.id
            ";
            $values[":questionnaire"] = $filters['questionnaire'];
        }
        if ($filters['project']) {
            $sqlInner .= "INNER JOIN question_answer_project
            ON question_answer_project.project = :project AND question_answer.id = question_answer_project.answer";
            $values[":project"] = $filters["project"];
        }
        
        if ($count) {
            $sql = "SELECT count(id)
                    FROM question_answer
                    $sqlInner
                    ";
            return (int) self::query($sql, $values)->fetchColumn();
        }

        $sql = "SELECT question_answer.*
                FROM question_answer
                INNER JOIN question ON question_answer.question = question.id
                $sqlInner
                ORDER BY question.order";

        // die(\sqldbg($sql, $values));
        $query = static::query($sql, $values);
        $answers = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);

        return $answers;
    }

}

