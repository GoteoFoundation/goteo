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

use Goteo\Util\Form\Type\BooleanType;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\DropfilesType;
use Goteo\Util\Form\Type\TextType;
use Goteo\Util\Form\Type\TypeaheadType;
use Goteo\Util\Form\Type\UrlType;
use Symfony\Component\Form\FormInterface;
use Goteo\Library\Forms\Model\ProjectStoryForm;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Model\Stories;
use Goteo\Model\Sphere;
use Goteo\Library\Forms\FormModelException;
use Goteo\Application\Lang;
use Goteo\Model\Image\Credits;

class AdminStoryEditForm extends ProjectStoryForm {

    public function createForm() {
        $builder = $this->getBuilder();
        /** @var Stories $story */
        $story = $this->getModel();
        parent::createForm();

        $builder
            ->add('image', DropfilesType::class, [
                'label' => 'story-field-image',
                'disabled' => $this->getReadonly(),
                'required' => true,
                'data' => $story->getImage(),
                'limit' => 1
            ])
            ->add('pool_image', DropfilesType::class, [
                'label' => 'story-field-pool-image',
                'disabled' => $this->getReadonly(),
                'data' => $story->getPoolImage(),
                'required' => false,
                'limit' => 1
            ])
            ->add('background_image', DropfilesType::class, [
                'label' => 'story-field-background-image',
                'disabled' => $this->getReadonly(),
                'data' => $story->getBackgroundImage(),
                'required' => false,
                'limit' => 1
            ])
            ->add('background_image_credits', TextType::class, [
                'label' => 'story-field-background-image-credits',
                'data' => Credits::get($story->background_image)->credits,
                'required' => false,
            ])
            ->add('review', TextType::class, [
                'label' => 'admin-stories-review',
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly()
            ])
            ->add('url', UrlType::class, [
                'label' => 'regular-url',
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly()
            ])
            ->add('project', TypeaheadType::class, [
                'label' => 'admin-project',
                'row_class' => 'extra',
                'required' => false,
                'disabled' => $this->getReadonly(),
                'sources' => 'project',
                'text' => ($story && $story->getProject()) ? $story->getProject()->name : null
            ])
            ->add('lang', ChoiceType::class, [
                'label' => 'regular-lang',
                'row_class' => 'extra',
                'choices' => $this->getChoices(Lang::listAll('name', false))
            ])
            ->add('pool', BooleanType::class, [
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly(),
                'label' => 'admin-stories-pool', // Form has integrated translations
                'color' => 'cyan', // bootstrap label-* (default, success, ...)
            ])
            ->add('landing_pitch', BooleanType::class, [
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly(),
                'label' => 'admin-stories-landing_pitch', // Form has integrated translations
                'color' => 'lilac', // bootstrap label-* (default, success, ...)
            ])
            ->add('landing_match', BooleanType::class, [
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly(),
                'label' => 'admin-stories-landing_match', // Form has integrated translations
                'color' => 'lilac', // bootstrap label-* (default, success, ...)
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'admin-stories-type',
                'required' => true,
                'expanded' => true,
                'row_class' => 'extra',
                'wrap_class' => 'col-xs-6',
                'choices' => $this->getChoices(Stories::getListTypes()),
                'constraints' => [
                    new Constraints\NotBlank()
                ]
            ])
            ->add('sphere', ChoiceType::class, [
                'label' => 'admin-title-sphere',
                'required' => true,
                'row_class' => 'extra',
                'wrap_class' => 'col-xs-6',
                'choices' => array_column(Sphere::getAll(), 'id', 'name'),
                'constraints' => [
                    new Constraints\NotBlank()
                ]
            ])
            ->add('active', BooleanType::class, [
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly(),
                'label' => 'admin-stories-active', // Form has integrated translations
                'color' => 'cyan', // bootstrap label-* (default, success, ...)
            ]);

        return $this;
    }

    private function getChoices(array $items) {
        $choices = [];

        foreach ($items as $k => $v) {
            $choices[$v] = $k;
        }

        return $choices;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $model = $this->getModel();

        $this->processImageChange($data['image'], $model->image);
        $this->processImageChange($data['background_image'], $model->background_image, false);
        $this->processImageChange($data['pool_image'], $model->pool_image, false);

        unset($data['image']);
        unset($data['background_image']);
        unset($data['pool_image']);
        $model->rebuildData($data, array_keys($form->all()));

        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        if ($model->background_image && $data['background_image_credits_credits']) {
            $model->background_image->setCredits($data['background_image_credits']);
        }

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }
}
