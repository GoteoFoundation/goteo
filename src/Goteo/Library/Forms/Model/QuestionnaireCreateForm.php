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
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Text;
use Goteo\Model\Questionnaire;
use Goteo\Util\Form\Type\BooleanType;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\DropfilesType;
use Goteo\Util\Form\Type\NumberType;
use Goteo\Util\Form\Type\SubmitType;
use Goteo\Util\Form\Type\TextareaType;
use Goteo\Util\Form\Type\TextType;

class QuestionnaireCreateForm extends AbstractFormProcessor implements FormProcessorInterface
{

    public function getConstraints(): array
    {
        return [];
    }

    public function addQuestion($question)
    {
        $config = $question->vars;

        if ($config->attr) { $config->attr = (array) $config->attr;
        }
        if ($config->type == DropfilesType::class) {
            $config->constraints = $this->getConstraints();
        }
        $builder = $this->getBuilder();
        $builder
            ->add(
                $question->id . '_typeofquestion', ChoiceType::class, [
                    'label' => Text::get('questionnaire-type-of-question'),
                    'choices' => Questionnaire::getTypes(),
                    'data' => Questionnaire::getQuestionTypeClass($config->type)
                ]
            )
            ->add(
                $question->id . '_required', BooleanType::class, [
                    'label' => Text::get('questionnaire-required'),
                    'data' => (bool)$config->required,
                    'required' => false
                ]
            )
            ->add(
                $question->id . '_hidden', BooleanType::class, [
                    'label' => Text::get('questionnaire-hidden'),
                    'data' => (bool)$config->hidden,
                    'required' => false
                ]
            )
            ->add(
                $question->id . '_max_score', NumberType::class, [
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
                $question->id . '_question', TextareaType::class, [
                    'label' => Text::get('questionnaire-text'),
                    'data' => $question->title,
                ]
            )
            ->add(
                $question->id . "_remove", SubmitType::class, [
                    'label' => Text::get('regular-delete'),
                    'icon_class' => 'fa fa-trash',
                    'span' => 'hidden-xs',
                    'attr' => [
                        'class' => 'pull-right btn btn-default remove-question',
                        'data-confirm' => Text::get('project-remove-reward-confirm')
                    ]
                ]
            )->add(
                $question->id . "_choice_answer", TextType::class, [
                    'label' => 'question-choice-answer'
            ]);

        if ($config->type == ChoiceType::class) {
            foreach ($config->vars->choices as $key => $value) {
                $builder
                    ->add(
                        $question->id . "_choice_" . $key, TextType::class, [
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

        $builder->add('add-question', SubmitType::class, [
                'label' => Text::get('questionnaire-add-question'),
                'attr' => ['class' => 'btn btn-lg btn-lilac text-uppercase add-question'],
                'icon_class' => 'icon icon-match-blog '
            ]
        )->add('submit', SubmitType::class, [
                'label' => 'regular-submit'
            ]
        );

        return $this;
    }

}
