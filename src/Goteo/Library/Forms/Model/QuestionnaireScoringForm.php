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
use Symfony\Component\Form\FormInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Text;
use Goteo\Library\Forms\FormModelException;
use Goteo\Model\Questionnaire\Answers;
use Goteo\Model\Questionnaire\Answer;
use Goteo\Model\Questionnaire\Question;
use Goteo\Model\Questionnaire;
use Goteo\Model\Contract\Document;

class QuestionnaireScoringForm extends AbstractFormProcessor implements FormProcessorInterface
{

    public function getConstraints($field)
    {
        $constraints = [];
        if($this->getFullValidation()) {
            // $constraints[] = new Constraints\NotBlank();
        }
        return $constraints;
    }

    public function createForm()
    {
        $questionnaire_answers = $this->getModel();
        $questionnaire = Questionnaire::get($questionnaire_answers->questionnaire);
        $answers = Answer::getByQuestionnaireAnswer($questionnaire_answers->id);

        $this->setReadonly(true);

        $builder = $this->getBuilder();
        foreach($answers as $index => $answer) {
          $question = $questionnaire->questions[$index];
          $type = $question->vars->type;

          $question->vars->label = $question->title;
          $builder->add("answer_" . $question->id, $type, [
            'label' => $question->title,
            'data' => ($type == "dropfiles")? Document::get($answer->answer) : $answer->answer,
            'disabled' => $this->getReadonly()
          ])->add("answer_" . $question->id . "_required", 'boolean', [
            'label' => Text::get('questionnaire-required'),
            'data' => $question->vars->required,
            'disabled' => $this->getReadonly()
          ])->add("answer_". $question->id . "_mark", 'number', [
            'label' => "Puntuacion"
          ]);
        }

        $builder->add(
            'submit', 'submit', [
            'label' => 'regular-submit',
            'attr' => ['class' => 'btn btn-lg btn-lilac text-uppercase'],
            'icon_class' => 'icon icon-match-blog '
            ]
        );

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false)
    {
        // if(!$form) { $form = $this->getBuilder()->getForm();
        // }
        // if(!$form->isValid() && !$force_save) { throw new FormModelException(Text::get('form-has-errors'));
        // }
        
        // // $data = $form->getData();
        // $data = array_intersect_key($form->getData(), $form->all());
        // $answers = new Answers();
        // $answers->project  = $this->model->project_id;
        // $answers->questionnaire = $this->model->id;
        // $answers->save();
        
        // foreach($data as $key => $value) {
        //     $answer = new Answer();
        //     $answer->questionnaire_answer = $answers->id;
        //     $answer->question = $key;
        //     $answer->answer = $value; 
        //     if (Question::get($key)->vars->type == "dropfiles") { 
        //         $answer->answer = $value[0]->name; 
        //     }
        //     $answer->save();
        // }

        // return $this;
    }
}
