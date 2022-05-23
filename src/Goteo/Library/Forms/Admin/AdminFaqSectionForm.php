<?php

/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library\Forms\Admin;

use Goteo\Application\Config;
use Goteo\Application\Lang;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Faq;
use Goteo\Model\Faq\FaqSection;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\DropfilesType;
use Goteo\Util\Form\Type\SubmitType;
use Goteo\Util\Form\Type\TextType;
use Goteo\Util\Form\Type\UrlType;
use Symfony\Component\Form\FormInterface;

class AdminFaqSectionForm extends AbstractFormProcessor
{
    public function createForm(): AdminFaqSectionForm
    {
        $model = $this->getModel();
        $builder = $this->getBuilder();

        $builder
            ->add('name', TextType::class, [
                'required' => true
            ])
            ->add('slug', TextType::class, [
                'required' => true
            ])
            ->add('icon', DropfilesType::class, [])
            ->add('banner_header', DropfilesType::class, [])
            ->add('button_action', TextType::class, [
                'required' => true
            ])
            ->add('button_url', UrlType::class, [
                'required' => true
            ])
            ->add('lang', ChoiceType::class, [
                'choices' => $this->getLanguagesAsChoices()
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'regular-submit',
                'attr' => ['class' => 'btn btn-cyan'],
                'icon_class' => 'fa fa-save'
            ]);

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false): AdminFaqSectionForm
    {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $model = $this->getModel();
        $model->rebuildData($data, array_keys($form->all()));
        $model->order = FaqSection::getListCount([]) + 1;

        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }
        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }


    private function getLanguagesAsChoices(): array
    {
        $choices = [];
        $languages = Lang::listAll('name', false);

        foreach ($languages as $key => $value) {
            $choices[$value] = $key;
        }

        return $choices;
    }
}
