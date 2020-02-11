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
use Goteo\Model\Questionnaire\Answer;
use Goteo\Model\Questionnaire\Question;
use Goteo\Model\Questionnaire;
use Goteo\Model\Contract\Document;
use Goteo\Model\Questionnaire\Score;

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
        // $this->scoring_answers = $this->getModel();
        $options = $this->getOptions();
        
        $scoring_answers = $options['scoring_answers'];
        $answers = $options['answers'];
        $questions = $options['questions'];

        $this->setReadonly(true);

        $builder = $this->getBuilder();
        foreach($answers as $index => $answer) {
          $question = $questions[$index];
          $type = $question->vars->type;

          $question->vars->label = $question->title;
          $builder->add($scoring_answers[$index]->id . "_mark", 'number', [
            'label' => Text::get('questionnaire-scoring-mark'),
            'data' => $scoring_answers[$index]->score,
            'attr' => [
              'class'=> 'border',
              'min' => 0,
              'max' => $question->max_score,
            ],
            'required' => true,
            'attr' => [
              'help' => Text::get('questionnaire-help-max-score', $question->max_score)
            ]
            
          ])
          ;
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
        if(!$form) { $form = $this->getBuilder()->getForm();
        }
        if(!$form->isValid() && !$force_save) { throw new FormModelException(Text::get('form-has-errors'));
        }

        $data = array_intersect_key($form->getData(), $form->all());
        foreach($data as $key => $val) {
          list($score_id, $field) = explode('_', $key);
          $score = Score::get($score_id);
          $score->score = $val;
          $score->save();
        }
        
        return $this;
    }
}
