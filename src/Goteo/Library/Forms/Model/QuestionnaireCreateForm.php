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
use Goteo\Model\Questionnaire;
use Goteo\Model\Questionnaire\Answers;
use Symfony\Component\Validator\Constraints;
use Goteo\Model\Contract\Document;

class QuestionnaireCreateForm extends AbstractFormProcessor implements FormProcessorInterface {

    public function getConstraints($field) {
        $constraints = [];
        if($this->getFullValidation()) {
            // $constraints[] = new Constraints\NotBlank();
        }
        return $constraints;
    }

    public function addQuestion($question) {

        $questionnaire = $this->getModel();

        if ($question->vars->attr) $question->vars->attr = (array) $question->vars->attr;
        if ($question->type == "dropfiles") {
            $question->vars->url = '/api/matcher/' . $questionnaire->matcher . '/project/' . $this->model->project_id . '/documents';
            $question->vars->constraints = $this->getConstraints('docs');
        }
        $builder = $this->getBuilder();
        
        $builder
            ->add($question->id . '_typeofquestion', 'choice', [
                'label' => Text::get('questionnaire-type-of-question'),
                'choices' => Questionnaire::getTypes(),
                'data' => $question->type
            ])
            ->add($question->id . '_required', 'boolean',[
                'label' => Text::get('questionnaire-required'),
                'data' => $question->vars->required ? true : false,
                'required' => false
            ])
            ->add($question->id . '_question', 'textarea', [
                'label' => Text::get('questionnaire-text'),
                'data' => $question->vars->label,
            ])
            ->add($question->id . "_remove", 'submit', [
                'label' => Text::get('regular-delete'),
                'icon_class' => 'fa fa-trash',
                'span' => 'hidden-xs',
                'attr' => [
                    'class' => 'pull-right btn btn-default remove-question',
                    'data-confirm' => Text::get('project-remove-reward-confirm')
                    ]
            ]);

    }
    
    public function createForm() {
        $questionnaire = $this->getModel();

        $builder = $this->getBuilder();
        foreach($questionnaire->vars as $question) {
            $this->addQuestion($question);
        }

        $builder->add('add-question', 'submit', [
            'label' => 'add-question',
            'attr' => ['class' => 'btn btn-lg btn-lilac text-uppercase add-question'],
            'icon_class' => 'icon icon-match-blog '
        ])->add('submit', 'submit', [
            'label' => 'regular-submit'
        ]);

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));
        
        $data = $form->getData();
        $answers = new Answers();
        $answers->project  = $this->model->project_id;
        $answers->questionnaire = $this->model->id;
        
        foreach($data as $key => $answer) {
            $answers->answer->{$key} = $answer;
        }

        $answers->save();
        $errors = [];
        return $this;
    }
}
