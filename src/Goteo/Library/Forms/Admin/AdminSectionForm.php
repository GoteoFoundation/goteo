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

use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\DropfilesType;
use Goteo\Util\Form\Type\TextareaType;
use Goteo\Util\Form\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Library\Forms\FormModelException;

use Goteo\Model\Node\NodeSections;

class AdminSectionForm extends AbstractFormProcessor {

    public function getConstraints(): array
    {
        return [new Constraints\NotBlank()];
    }

    public function createForm() {
        $model = $this->getModel();
        $builder = $this->getBuilder();

        $builder
            ->add('section', ChoiceType::class, [
                'disabled' => $this->getReadonly(),
                'required' => true,
                'label' => 'admin-channelsection-section',
                'choices' => $this->getChoices(NodeSections::getSectionNames()),
            ])
            ->add('main_title', TextType::class, [
                'disabled' => $this->getReadonly(),
                'required' => false,
                'label' => 'regular-title'
            ])
            ->add('main_description', TextareaType::class, [
                'disabled' => $this->getReadonly(),
                'required' => false,
                'label' => 'regular-description'
            ])
            ->add('main_button', TextareaType::class, [
                'disabled' => $this->getReadonly(),
                'required' => false,
                'label' => 'admin-channelsection-button'
            ])
            ->add('main_image', DropfilesType::class, array(
                'required' => false,
                'limit' => 1,
                'data' => [$model->main_image ? $model->getMainImage() : null],
                'label' => 'regular-image',
                'accepted_files' => 'image/jpeg,image/png,image/svg+xml'
            ))
            ;

        return $this;
    }

    private function getChoices($items)
    {
        $choices = [];

        foreach ($items as $k => $v) {
            $choices[$v] = $k;
        }

        return $choices;
    }

    public function save(FormInterface $form = null, $force_save = false) {

        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) {
            throw new FormModelException(Text::get('form-has-errors'));
        }

        $data = $form->getData();
        $model = $this->getModel();

        $this->processImageChange($data['main_image'], $model->main_image, false);

        unset($data['main_image']);
        $model->rebuildData($data, array_keys($form->all()));
        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }
        return $this;
    }
}
