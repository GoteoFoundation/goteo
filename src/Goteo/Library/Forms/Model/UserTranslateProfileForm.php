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
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Library\Forms\FormModelException;

class UserTranslateProfileForm extends AbstractFormProcessor implements FormProcessorInterface {

    public function createForm() {
        $user = $this->getModel();

        $builder = $this->getBuilder()
            ->add('name', 'text', [
                'label' => 'regular-name',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => $user->name]
            ])
            ->add('about', 'textarea', [
                'label' => 'profile-field-about',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => $user->about]
            ])
            ;
        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $user = $this->getModel();
        $lang = $this->getOption('lang');

        $data = array_intersect_key($form->getData(), $form->all());
        $errors = [];
        if(!$user->setLang($lang, $data, $errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(',',$errors)));
        }
        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));
        return $this;
    }
}
