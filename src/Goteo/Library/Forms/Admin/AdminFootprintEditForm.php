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
use Goteo\Util\Form\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Model\Sdg;
use Goteo\Model\Category;
use Goteo\Model\SocialCommitment;
use Goteo\Library\Forms\FormModelException;

class AdminFootprintEditForm extends AbstractFormProcessor {

    public function getConstraints() {
        return [new Constraints\NotBlank()];
    }

    public function createForm() {
        $model = $this->getModel();
        $builder = $this->getBuilder();
        $sdgs = [];

        foreach(Sdg::getList([],0,100) as $s) {
            $sdgs['<img src="'.$s->getIcon()->getLink().'" class="icon"> '.$s->name] = $s->id;
        }

        $builder
            ->add('name', TextType::class, [
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints(),
                'label' => 'regular-name'
            ])
            ->add('title', TextType::class, [
                'disabled' => $this->getReadonly(),
                'required' => false,
                'label' => 'regular-title'
            ])
            ->add('description', TextType::class, [
                'disabled' => $this->getReadonly(),
                'required' => false,
                'label' => 'regular-description'
            ])
            ->add('icon', DropfilesType::class, array(
                'required' => false,
                'limit' => 1,
                'data' => [$model->icon ? $model->getIcon() : null],
                'label' => 'admin-title-icon',
                'accepted_files' => 'image/jpeg,image/gif,image/png,image/svg+xml',
                'attr' => [
                    'help' => Text::get('admin-categories-if-empty-then-asset', '<img src="'.$model->getIcon(true)->getLink(64,64).'" class="icon">')
                ]
            ))
            ->add('sdgs', ChoiceType::class, array(
                'label' => 'admin-title-sdgs',
                'data' => array_column($model->getSdgs(), 'id'),
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choices' => $sdgs,
                'choices_label_escape' => false,
                'wrap_class' => 'col-xs-6 col-xxs-12'
            ))
            ->add('categories', ChoiceType::class, array(
                'label' => 'admin-title-categories',
                'data' => array_column($model->getCategories(), 'id'),
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choices' => array_column(Category::getAll(), 'id', 'name'),
                'choices_label_escape' => false,
                'wrap_class' => 'col-xs-6 col-xxs-12'
            ))
            ->add('social_commitments', ChoiceType::class, array(
                'label' => 'admin-title-social_commitments',
                'data' => array_column($model->getSocialCommitments(), 'id'),
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choices' => array_column(SocialCommitment::getAll(), 'id', 'name'),
                'choices_label_escape' => false,
                'wrap_class' => 'col-xs-6 col-xxs-12'
            ));

        return $this;
    }


    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $model = $this->getModel();

        if ($data['icon'] && is_array($data['icon'])) {
            if ($data['icon']['removed'] && $model->icon == current($data['icon']['removed'])->id)
                $model->icon = null;

            if ($data['icon']['uploads'] && is_array($data['icon']['uploads']))
                $model->icon = $data['icon']['uploads'][0];
        }

        unset($data['icon']);

        $model->rebuildData($data, array_keys($form->all()));

        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }
        $model->replaceSdgs($data['sdgs']);
        $model->replaceCategories($data['categories']);
        $model->replaceSocialCommitments($data['social_commitments']);

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }
}
