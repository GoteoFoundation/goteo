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

use Goteo\Application\Config;
use Goteo\Application\Message;

class Answers extends \Goteo\Core\Model
{

    public
        $questionnaire,
        $project;

    protected $Table = 'questionnaire_answer';

    public function validate(&$errors = array())
    {
        return true;
    }

    public function save(&$errors = array())
    {
        if (!$this->validate($errors)) { return false;
        }

        $fields = array(
            'questionnaire',
            'project',
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

    /**
     * Get answers by questionnaire answer id
     *
     * @param  int $id questionnaire answer id.
     * @return Answer object
     */
    public static function getByQuestionnaireProject($qid, $pid) {

        $query = static::query('SELECT * FROM questionnaire_answer WHERE questionnaire = :qid AND project = :pid', array(':qid' => $qid, ':pid' => $pid));
        $questionnaire_answer = $query->fetchObject(__CLASS__);

        return $questionnaire_answer;

    }

}

