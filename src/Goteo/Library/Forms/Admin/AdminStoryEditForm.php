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

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\FormInterface;
use Goteo\Library\Forms\Model\ProjectStoryForm;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Model\Call;
use Goteo\Model\Stories;
use Goteo\Model\Sphere;
use Goteo\Library\Forms\FormModelException;
use Goteo\Application\Lang;
use Goteo\Model\Image;
use Goteo\Model\Image\Credits;

class AdminStoryEditForm extends ProjectStoryForm {

    public function createForm() {
        $builder = $this->getBuilder();
        $options = $builder->getOptions();
        $story = $this->getModel();
        $data = $options['data'];
        parent::createForm();
        $builder
            ->add('image', 'dropfiles', [
                'label' => 'story-field-image',
                'disabled' => $this->getReadonly(),
                'required' => true,
                'data' => $story->getImage(),
                'limit' => 1
            ])
            ->add('pool_image', 'dropfiles', [
                'label' => 'story-field-pool-image',
                'disabled' => $this->getReadonly(),
                'data' => $story->getPoolImage(),
                'required' => false,
                'limit' => 1
            ])
            ->add('background_image', 'dropfiles', [
                'label' => 'story-field-background-image',
                'disabled' => $this->getReadonly(),
                'data' => $story->getBackgroundImage(),
                'required' => false,
                'limit' => 1
            ])
            ->add('background_image_credits', 'text', array(
                'label' => 'story-field-background-image-credits',
                'data' => Credits::get($story->background_image)->credits,
                'required' => false,
            ))
            ->add('review', 'text', [
                'label' => 'admin-stories-review',
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly()
            ])
            ->add('url', 'url', [
                'label' => 'regular-url',
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly()
            ])
            ->add('project', 'typeahead', [
                'label' => 'admin-project',
                'row_class' => 'extra',
                'required' => false,
                'disabled' => $this->getReadonly(),
                'sources' => 'project',
                'text' => ($story && $story->getProject()) ? $story->getProject()->name : null
            ])
            ->add('lang', 'choice', array(
                'label' => 'regular-lang',
                'row_class' => 'extra',
                'choices' => Lang::listAll('name', false)
            ))
            ->add('pool', 'boolean', array(
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly(),
                'label' => 'admin-stories-pool', // Form has integrated translations
                'color' => 'cyan', // bootstrap label-* (default, success, ...)
            ))
            ->add('landing_pitch', 'boolean', array(
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly(),
                'label' => 'admin-stories-landing_pitch', // Form has integrated translations
                'color' => 'lilac', // bootstrap label-* (default, success, ...)
            ))
            ->add('landing_match', 'boolean', array(
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly(),
                'label' => 'admin-stories-landing_match', // Form has integrated translations
                'color' => 'lilac', // bootstrap label-* (default, success, ...)
            ))
            ->add('type', 'choice', array(
                'label' => 'admin-stories-type',
                'required' => true,
                'expanded' => true,
                'row_class' => 'extra',
                'wrap_class' => 'col-xs-6',
                'choices' => Stories::getListTypes(),
                'constraints' => [
                    new Constraints\NotBlank()
                ]
            ))
            ->add('sphere', 'choice', array(
                'label' => 'admin-title-sphere',
                'required' => true,
                // 'expanded' => true,
                'row_class' => 'extra',
                'wrap_class' => 'col-xs-6',
                'choices' => array_column(Sphere::getAll(), 'name', 'id'),
                'constraints' => [
                    new Constraints\NotBlank()
                ]
            ))
            ->add('active', 'boolean', array(
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly(),
                'label' => 'admin-stories-active', // Form has integrated translations
                'color' => 'cyan', // bootstrap label-* (default, success, ...)
            ))
            ;


        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $model = $this->getModel();

        if ($data['image'] && is_array($data['image'])) {
            if ($data['image']['uploads'] && is_array($data['image']['uploads']))
                $model->image = $data['image']['uploads'][0];
        }

        if ($data['background_image'] && is_array($data['background_image'])) {
            if ($data['background_image']['removed'] && $model->background_image == current($data['background_image']['removed'])->id)
                $model->background_image = null;

            if ($data['background_image']['uploads'] && is_array($data['background_image']['uploads']))
                $model->background_image = $data['background_image']['uploads'][0];
        }

        if ($data['pool_image'] && is_array($data['pool_image'])) {
            if ($data['pool_image']['removed'] && $model->pool_image == current($data['pool_image']['removed'])->id)
                $model->pool_image = null;

            if ($data['pool_image']['uploads'] && is_array($data['pool_image']['uploads']))
                $model->pool_image = $data['pool_image']['uploads'][0];
        }

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
