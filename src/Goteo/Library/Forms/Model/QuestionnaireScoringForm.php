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

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Library\Forms\FormProcessorInterface;
use Goteo\Util\Form\Type\DropfilesType;
use Goteo\Util\Form\Type\NumberType;
use Goteo\Util\Form\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Text;
use Goteo\Library\Forms\FormModelException;
use Goteo\Model\Questionnaire\Question;
use Goteo\Model\Contract\Document;
use Goteo\Model\Questionnaire\Score;

class QuestionnaireScoringForm extends AbstractFormProcessor implements FormProcessorInterface
{

    public function createForm()
    {
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
          $builder->add($scoring_answers[$index]->id . "_mark", NumberType::class, [
            'label' => Text::get('questionnaire-scoring-mark'),
            'data' => $scoring_answers[$index]->score,
            'required' => true,
            'attr' => [
                'class'=> 'border',
                'help' => Text::get('questionnaire-help-max-score', $question->max_score),
                'min' => 0,
                'max' => $question->max_score,
            ]
          ]);

          if ($type == "dropfiles") {
            try {
              $doc = Document::getByName($answer->answer);
            } catch(ModelNotFoundException $e) {
              $doc = null;
            }

            $builder->add(
              $scoring_answers[$index]->id . "_file", DropfilesType::class, [
                'label' => 'Resposta',
                'data' => Document::getByName($answer->answer),
                'type' => 'pdf',
                'disabled' => $this->getReadonly()
              ]
            );
          }
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
        if(!$form) { $form = $this->getBuilder()->getForm();
        }
        if(!$form->isValid() && !$force_save) { throw new FormModelException(Text::get('form-has-errors'));
        }

        $data = array_intersect_key($form->getData(), $form->all());
        foreach($data as $key => $val) {
          list($score_id, $field) = explode('_', $key);
          $score = Score::get($score_id);
          $question = Question::get($score->question);
          $score->score = ($val < 0)? 0 : (($val > $question->max_score)? $question->max_score : $val);
          $score->save();
        }

        return $this;
    }
}
