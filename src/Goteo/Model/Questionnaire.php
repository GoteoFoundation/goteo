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
    $lang,
    $questions;

    static public function getTypes()
    {
        return [
            'textarea' => Text::get('questionnaire-textarea'), 
            'boolean' => Text::get('questionnaire-boolean'),
            'dropfiles' => Text::get('questionnaire-dropfiles')
            // 'choice' => Text::get('questionnaire-choice')
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

        $query = static::query('SELECT questionnaire.* 
                                FROM questionnaire 
                                INNER JOIN questionnaire_matcher
                                ON questionnaire.id = questionnaire_matcher.questionnaire AND questionnaire_matcher.matcher = :matcher', array(':matcher' => $mid));
        $questionnaire = $query->fetchObject(__CLASS__);
        if ($questionnaire instanceOf Questionnaire)
            $questionnaire->questions = Question::getByQuestionnaire($questionnaire->id);

        return $questionnaire;

    }

    public function getMaxScore() {
        $query = static::query('SELECT sum(question.max_score)
                                FROM question 
                                WHERE question.questionnaire = :questionnaire
                                ', [':questionnaire' => $this->id]);
        
        return $query->fetchColumn();
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
            'lang'
            );

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);

            if ($this->matcher) {
                $sql = "REPLACE INTO questionnaire_matcher VALUES(:questionnaire, :matcher)";
                $values = [":questionnaire" => $this->id, ":matcher" => $this->matcher];

                // die(\sqldbg($sql, $values));
                static::query($sql, $values);
            }

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Save error " . $e->getMessage();
            return false;
        }
    }

    public function removeLang($lang) {
        if ($this->questions) {
            foreach($this->questions as $question) {
                $question->removeLang($lang);
            }
        }
    }

        /**
     * Returns percent (from 0 to 100) translations
     * by grouping all items sharing some common keys
     */
    public function getLangsGroupPercent($lang, array $keys) {
        $percent = 0;
        if ($this->questions) {
            foreach($this->questions as $question) {
                $percent += $question->getLangsGroupPercent($lang, ['title']);
            }
            $percent = $percent/count($this->questions);
        }

        return $percent;
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


