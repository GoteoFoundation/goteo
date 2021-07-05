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

class QuestionnaireCreateTranslateForm extends AbstractFormProcessor implements FormProcessorInterface
{

    public function getConstraints($field)
    {
        $constraints = [];
        if($this->getFullValidation()) {
            // $constraints[] = new Constraints\NotBlank();
        }
        return $constraints;
    }

    public function addQuestion($question, $lang)
    {
        $builder = $this->getBuilder();
        $questionLang = $question->getLang($lang);
        $builder
            ->add(
                $question->id . '_title', 'textarea', [
                'label' => Text::get('questionnaire-text'),
                'required' => false,
                'data' => $questionLang ? $questionLang->title : '',
            ]);
    }
    
    public function createForm()
    {
        $questionnaire = $this->getModel();
        $builder = $this->getBuilder();
        $lang = $this->getOption('lang');

        if ($questionnaire->questions) {
            foreach($questionnaire->questions as $question) {
                $this->addQuestion($question, $lang);
            }
        }
        
        $builder->add(
            'submit', 'submit', [
                'label' => 'regular-submit'
                ]
        );

        return $this;
    }

}
