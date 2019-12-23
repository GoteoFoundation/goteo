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
      $questionnaire_answer,
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
            'questionnaire_answer',
            'question',
            'answer'
            );

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);
            return true;
        } catch(\PDOException $e) {
            Message::error($e->getMessage());
            $errors[] = "Save error " . $e->getMessage();
            return false;
        }

    }

}

