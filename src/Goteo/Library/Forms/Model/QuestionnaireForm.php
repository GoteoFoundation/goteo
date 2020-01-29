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
use Symfony\Component\Validator\Constraints;
use Goteo\Model\Contract\Document;

class QuestionnaireForm extends AbstractFormProcessor implements FormProcessorInterface
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
        $questionnaire = $this->getModel();

        $builder = $this->getBuilder();
        foreach((array) $questionnaire->questions as $question) {
            $type = $question->vars->type;
            unset($question->vars->type);
            if ($question->vars->attr) { $question->vars->attr = (array) $question->vars->attr;
            }
            if ($question->vars->type == "dropfiles") {
                
                $question->vars->vars->url = '/api/matcher/' . $questionnaire->matcher . '/project/' . $this->model->project_id . '/documents';
                $question->vars->vars->constraints = $this->getConstraints('docs');
            }
            $question->vars->label = $question->title;
            $builder->add($question->id, $type, (array) $question->vars);
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
        
        // $data = $form->getData();
        $data = array_intersect_key($form->getData(), $form->all());
        $answers = new Answers();
        $answers->project  = $this->model->project_id;
        $answers->questionnaire = $this->model->id;
        $answers->save();
        
        foreach($data as $key => $value) {
            $answer = new Answer();
            $answer->questionnaire_answer = $answers->id;
            $answer->question = $key;
            $answer->answer = $value; 
            if (Question::get($key)->vars->type == "dropfiles") { 
                $document = new Document();
                $document = $value[0];
                $document->save();
                $answer->answer = $value[0]->name; 
            }
            $answer->save();
        }

        return $this;
    }
}
