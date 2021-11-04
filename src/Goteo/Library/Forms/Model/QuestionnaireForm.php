<?php

/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library\Forms\Model;

use Goteo\Library\Forms\FormProcessorInterface;
use Goteo\Model\Questionnaire;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\DropfilesType;
use Goteo\Util\Form\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Text;
use Goteo\Library\Forms\FormModelException;
use Goteo\Model\Questionnaire\Answer;
use Goteo\Model\Questionnaire\Answer\AnswerOptions;
use Goteo\Model\Questionnaire\Question;
use Symfony\Component\Validator\Constraints;

class QuestionnaireForm extends AbstractFormProcessor implements FormProcessorInterface
{

    public function createForm()
    {
        $questionnaire = $this->getModel();
        $builder = $this->getBuilder();

        foreach((array) $questionnaire->questions as $question) {
            if ($question->vars->hidden)
                continue;
            else {
                unset($question->vars->hidden);
            }

            $type = $question->vars->type;
            unset($question->vars->type);
            if ($question->vars->attr) {
                $question->vars->attr = (array)$question->vars->attr;
            }

            if ($type == DropfilesType::class) {
                $question->vars->accepted_files = 'image/jpeg,image/gif,image/png,application/pdf';
                $question->vars->constraints = [
                    new Constraints\Count(['max' => 1]),
                ];
                $question->vars->type = DropfilesType::TYPE_DOCUMENT;
            }
            if ($type == ChoiceType::class) {
                $question->vars->choices = array_column($question->getChoices(), 'option', 'id');
            }

            $question->vars->label = $question->title;
            $builder->add(
                $question->id,
                Questionnaire::getQuestionTypeClass($type),
                (array) $question->vars
            );
        }
        $builder->add(
            'submit', SubmitType::class, [
                'label' => 'regular-submit',
                'attr' => ['class' => 'btn btn-lg btn-lilac text-uppercase'],
                'icon_class' => 'icon icon-match-blog '
            ]
        );

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false)
    {
        if (!$form) {
            $form = $this->getBuilder()->getForm();
        }
        if (!$form->isValid() && !$force_save) {
            throw new FormModelException(Text::get('form-has-errors'));
        }

        $questionnaire = $this->getModel();
        $questions = Question::getByQuestionnaire($questionnaire->id);
        $questions = array_column($questions, NULL, 'id');
        $index = 0;

        if ($answers = Answer::getList(['project' => $this->model->project_id, 'questionnaire' => $questionnaire->id]))
            $answers = array_column($answers, NULL, 'question');

        $data = array_intersect_key($form->getData(), $form->all());

        foreach($data as $key => $value) {
            $question = $questions[$key];

            if ($question->vars->hidden)
                continue;

            $answer = ($answers[$question->id])?: new Answer();
            $answer->project  = $this->model->project_id;
            $answer->question = $key;

            $type = $question->vars->type;
            if ($type != ChoiceType::class)
                $answer->answer = $value;

            if ($type == DropfilesType::class) {
                if($value[0] && $err = $value[0]->getUploadError()) {
                    throw new FormModelException(Text::get('form-sent-error', $err));
                }
                $answer->answer = $value['uploads'][0]->name;
            }
            $answer->save();

            if ($type == ChoiceType::class) {
                if ($answer_options = AnswerOptions::getList(['answer' => $answer->id])) {
                    foreach ($answer_options as $index => $answer_option) {
                        $answer_option->dbDelete(['answer', 'option']);
                    }
                }

                if (is_array($value)) {
                    foreach ($value as $index => $option) {
                        $answer_option = new AnswerOptions();
                        $answer_option->answer = $answer->id;
                        $answer_option->option = $option;
                        $answer_option->save();
                    }
                } else {
                    $answer_option = new AnswerOptions();
                    $answer_option->answer = $answer->id;
                    $answer_option->option = $value;
                    $answer_option->save();
                }
            }
            ++$index;
        }

        return $this;
    }
}
