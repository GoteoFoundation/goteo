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

class QuestionnaireCreateForm extends AbstractFormProcessor implements FormProcessorInterface
{

    public function getConstraints($field)
    {
        $constraints = [];
        if($this->getFullValidation()) {
            // $constraints[] = new Constraints\NotBlank();
        }
        return $constraints;
    }

    public function delQuestion($id)
    {

        $this->getBuilder()
            ->remove("{$id}_typeofquestion")
            ->remove("{$id}_required")
            ->remove("{$id}_question")
            ->remove("{$id}_remove");
    }

    public function addQuestion($question)
    {
        $config = $question->vars;
        
        if ($config->attr) { $config->attr = (array) $config->attr;
        }
        if ($config->type == "dropfiles") {
            $config->url = '/api/questionnaire/documents';
            $config->constraints = $this->getConstraints('docs');
        }
        $builder = $this->getBuilder();
        $builder
            ->add(
                $question->id . '_typeofquestion', 'choice', [
                'label' => Text::get('questionnaire-type-of-question'),
                'choices' => Questionnaire::getTypes(),
                'data' => $config->type
                ]
            )
            ->add(
                $question->id . '_required', 'boolean', [
                'label' => Text::get('questionnaire-required'),
                'data' => $config->required ? true : false,
                'required' => false
                ]
            )
            ->add(
                $question->id . '_hidden', 'boolean', [
                'label' => Text::get('questionnaire-hidden'),
                'data' => $config->hidden ? true : false,
                'required' => false
                ]
            )
            ->add(
                $question->id . '_max_score', 'number', [
                'label' => Text::get('questionnaire-max-score'),
                'data' => $question->max_score,
                'required' => true,
                'attr' => [
                    'min' => 0,
                    'help' => Text::get('questionnaire-max-score-help')
                    ]
                ]
            )
            ->add(
                $question->id . '_question', 'textarea', [
                'label' => Text::get('questionnaire-text'),
                'data' => $question->title,
                ]
            )
            ->add(
                $question->id . "_remove", 'submit', [
                'label' => Text::get('regular-delete'),
                'icon_class' => 'fa fa-trash',
                'span' => 'hidden-xs',
                'attr' => [
                    'class' => 'pull-right btn btn-default remove-question',
                    'data-confirm' => Text::get('project-remove-reward-confirm')
                    ]
                ]
            )->add(
                $question->id . "_choice_answer", 'text', [
                    'label' => 'question-choice-answer'
            ]);

        if ($config->type == "choice") {
            foreach ($config->vars->choices as $key => $value) {
                $builder
                    ->add(
                        $question->id . "_choice_" . $key, 'text', [
                            'label' => 'question-choice-answer',
                            'data' => $value
                        ]
                    );
            }
        }

    }
    
    public function createForm()
    {
        $questionnaire = $this->getModel();
        $builder = $this->getBuilder();

        if ($questionnaire->questions) {
            foreach($questionnaire->questions as $question) {
                $this->addQuestion($question);
            }
        }
        
        $builder->add(
            'add-question', 'submit', [
            'label' => Text::get('questionnaire-add-question'),
            'attr' => ['class' => 'btn btn-lg btn-lilac text-uppercase add-question'],
            'icon_class' => 'icon icon-match-blog '
            ]
        )->add(
            'submit', 'submit', [
                'label' => 'regular-submit'
                ]
        );

        return $this;
    }

}
