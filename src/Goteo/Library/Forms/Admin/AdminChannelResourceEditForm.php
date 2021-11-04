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
use Goteo\Util\Form\Type\MarkdownType;
use Goteo\Util\Form\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Text;
use Goteo\Model\Node;
use Goteo\Model\Node\NodeResourceCategory;
use Goteo\Library\Forms\FormModelException;

class AdminChannelResourceEditForm extends AbstractFormProcessor {

    public function createForm() {
        $builder = $this->getBuilder();
        $resource = $this->getModel();
        $categories = [];

        foreach(NodeResourceCategory::getList() as $c) {
            $categories[$c->id] = $c->name;
        }

        parent::createForm();
        $builder
            ->add('title', TextType::class, [
                'label' => 'regular-title',
                'required' => true,
                'disabled' => $this->getReadonly()
            ])
           ->add('description', MarkdownType::class, [
                'label' => 'regular-description',
                'required' => true,
                'attr' => [
                    'rows' => 8
                ]
            ])
           ->add('action', TextType::class, [
                'label' => 'admin-title-action-url',
                'required' => true,
                'disabled' => $this->getReadonly()
            ])
            ->add('action_url', TextType::class, [
                'label' => 'admin-title-download-url',
                'required' => true,
                'disabled' => $this->getReadonly()
            ])
            ->add('image', DropfilesType::class, [
                'required' => true,
                'limit' => 1,
                'data' => [$resource->image ? $resource->getImage() : null],
                'label' => 'admin-title-image',
                'accepted_files' => 'image/jpeg,image/gif,image/png,image/svg+xml'
            ])
            ->add('node_id', ChoiceType::class, [
                'label' => 'admin-title-channel',
                'required' => true,
                'expanded' => true,
                'row_class' => 'extra',
                'wrap_class' => 'col-xs-6',
                'choices' => Node::getList()
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'admin-title-resource-category',
                'required' => true,
                'expanded' => true,
                'row_class' => 'extra',
                'wrap_class' => 'col-xs-6',
                'choices' => $categories
            ]);


        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $model = $this->getModel();

        if ($data['image']['removed'])
            if ($model->image->id == current($data['image']['removed'])->id)
                $model->image = null;

        if ($data['image']['uploads'])
            $model->image = $data['image']['uploads'];

        unset($data['image']);

        $model->rebuildData($data, array_keys($form->all()));

        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }

}
