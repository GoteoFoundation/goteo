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
use Goteo\Library\Forms\AbstractFormProcessor;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Model\Node;
use Goteo\Model\Node\NodeResource;
use Goteo\Model\Node\NodeResourceCategory;
use Goteo\Library\Forms\FormModelException;
use Goteo\Application\Lang;

class AdminChannelResourceEditForm extends AbstractFormProcessor {

    public function createForm() {
        $builder = $this->getBuilder();
        $options = $builder->getOptions();
        $resource = $this->getModel();
        $data = $options['data'];

        $categories = [];
        foreach(NodeResourceCategory::getList() as $c) {
            $categories[$c->id] = $c->name;
        }

        parent::createForm();
        $builder
            ->add('title', 'text', [
                'label' => 'regular-title',
                'required' => true,
                'disabled' => $this->getReadonly()
            ])
           ->add('description', 'markdown', [
                'label' => 'regular-description',
                'required' => true,
                'attr' => [
                    'rows' => 8
                ]
            ])
           ->add('action', 'text', [
                'label' => 'admin-title-action-url',
                'required' => true,
                'disabled' => $this->getReadonly()
            ])
            ->add('action_url', 'text', [
                'label' => 'admin-title-download-url',
                'required' => true,
                'disabled' => $this->getReadonly()
            ]) 
            ->add('image', 'dropfiles', array(
                'required' => true,
                'limit' => 1,
                'data' => [$resource->image ? $resource->getImage() : null],
                'label' => 'admin-title-image',
                'accepted_files' => 'image/jpeg,image/gif,image/png,image/svg+xml',
                'constraints' => array(
                    new Constraints\Count(array('max' => 1))
                )
            ))
            ->add('node_id', 'choice', array(
                'label' => 'admin-title-channel',
                'required' => true,
                'expanded' => true,
                'row_class' => 'extra',
                'wrap_class' => 'col-xs-6',
                'choices' => Node::getList()
            ))
            ->add('category', 'choice', array(
                'label' => 'admin-title-resource-category',
                'required' => true,
                'expanded' => true,
                'row_class' => 'extra',
                'wrap_class' => 'col-xs-6',
                'choices' => $categories
            ))
            
            ;


        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
       
        $model = $this->getModel();
        $model->rebuildData($data, array_keys($form->all()));

        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }

}
