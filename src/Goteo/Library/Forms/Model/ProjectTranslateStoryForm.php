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
use Goteo\Util\Form\Type\TextareaType;
use Goteo\Util\Form\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Text;
use Goteo\Library\Forms\FormModelException;

class ProjectTranslateStoryForm extends AbstractFormProcessor implements FormProcessorInterface {

    public function createForm() {
        $story = $this->getModel();

        $builder = $this->getBuilder()
            ->add('title', TextType::class, [
                'label' => 'story-field-author-organization',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => $story->title]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'story-field-description',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => $story->description, 'rows' => 4]
            ]);

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $story = $this->getModel();
        $lang = $this->getOption('lang');

        $data = array_intersect_key($form->getData(), $form->all());
        $errors = [];
        $story->lang = $lang;
        if(!$story->setLang($lang, $data, $errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(',',$errors)));
        }
        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));
        return $this;
    }
}
