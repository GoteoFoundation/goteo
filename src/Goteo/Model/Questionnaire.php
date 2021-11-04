<?php

namespace Goteo\Model;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Core\Model;
use Goteo\Library\Text;
use Goteo\Model\Questionnaire\Question;
use Goteo\Util\Form\Type\BooleanType;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\DropfilesType;
use Goteo\Util\Form\Type\TextareaType;

class Questionnaire extends Model
{
    public $id;
    public $lang;
    public $questions;

    static public function getTypes(): array
    {
        return [
            Text::get('questionnaire-textarea') => TextareaType::class,
            Text::get('questionnaire-boolean') => BooleanType::class,
            Text::get('questionnaire-dropfiles') => DropfilesType::class,
            Text::get('questionnaire-choice') => ChoiceType::class
        ];
    }

    static public function getQuestionTypeClass(?string $type): ?string
    {
        if (self::hasOldTypeNamespace($type)) {
            $questionnaireTypeIndex = Text::get('questionnaire-' . $type);
            return self::getTypes()[$questionnaireTypeIndex];
        }

        return $type;
    }

    static private function hasOldTypeNamespace(?string $type): bool
    {
        return !in_array($type, self::getTypes());
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
        $query = static::query('SELECT * FROM questionnaire WHERE id = :id', array(':id' => $id));
        $questionnaire = $query->fetchObject(__CLASS__);

        if (!$questionnaire instanceOf Questionnaire) {
            throw new ModelNotFoundException();
        }

        $questionnaire->questions = Question::getByQuestionnaire($id);

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
        $query = static::query('SELECT questionnaire.*
                                FROM questionnaire
                                INNER JOIN questionnaire_matcher
                                ON questionnaire.id = questionnaire_matcher.questionnaire AND questionnaire_matcher.matcher = :matcher', array(':matcher' => $mid));
        $questionnaire = $query->fetchObject(__CLASS__);
        if ($questionnaire instanceOf Questionnaire)
            $questionnaire->questions = Question::getByQuestionnaire($questionnaire->id);

        return $questionnaire;
    }

    /**
     * Get data about questionnaire by matcher id
     *
     * @param  int $id matcher id.
     * @return Questionnaire object
     */
    static public function getByChannel($cid)
    {
        $lang = Lang::current();
        $query = static::query('SELECT questionnaire.*
                                FROM questionnaire
                                INNER JOIN questionnaire_channel
                                ON questionnaire.id = questionnaire_channel.questionnaire AND questionnaire_channel.channel = :channel', array(':channel' => $cid));
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
     * @param  array $errors
     * @return bool   true|false
     */
    public function save(&$errors = array())
    {
        if (!$this->validate($errors)) {
            return false;
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

                static::query($sql, $values);
            }

            if ($this->channel) {
                $sql = "REPLACE INTO questionnaire_channel VALUES(:questionnaire, :channel)";
                $values = [":questionnaire" => $this->id, ":channel" => $this->channel];

                static::query($sql, $values);
            }

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Save error " . $e->getMessage();
            return false;
        }
    }

    public function isAnswered($project_id)
    {
        $sql = 'SELECT DISTINCT(qap.project)
        FROM question_answer_project qap
        INNER JOIN question_answer qa ON qa.id  = qap.answer
        INNER JOIN question q ON q.id = qa.question
        WHERE qap.project = :project AND q.questionnaire = :id';

        $values = [':id' => $this->id, ':project' => $project_id];
        $query = static::query($sql, $values);

        return $query->fetchColumn();
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
     * @param  array $errors Errores devueltos pasados por referencia.
     * @return bool   true|false
     */
    public function validate(&$errors = array())
    {
        return true;
    }

    /**
     * Returns if the questionnaire has questions to show
     * @return bool true|false
     */
    public function hasQuestionsToShow() {
        $questions = $this->questions;

        $questionsToShow = false;
        foreach ($questions as $question) {
            if (!$question->vars->hidden) {
                return true;
            }
        }

        return $questionsToShow;
    }

}
