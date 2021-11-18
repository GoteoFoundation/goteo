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

use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Image;
use Goteo\Model\ImpactData;
use Goteo\Util\Form\Type\DropfilesType;
use Goteo\Util\Form\Type\TextType;
use Goteo\Util\Form\Type\TextareaType;
use Goteo\Util\Form\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints;

class ImpactDataForm extends AbstractFormProcessor {

    public function createForm() {

        $model = $this->getModel();
        $builder = $this->getBuilder();
        $options = $builder->getOptions();
        $data = $options['data'];

        $builder
            ->add('title', TextType::class, array(
                'label' => 'regular-title',
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
            ))
            ->add('data', TextType::class, array(
                'label' => 'regular-data',
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
            )) ->add('data_unit', TextType::class, array(
                'label' => 'regular-data-unit',
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
            ))
            ->add('description', TextareaType::Class, array(
                'label' => 'regular-description',
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                )
            ))
            ->add('image', DropfilesType::class, [
                'label' => 'regular-image',
                'data' => [ $model->image ? $model->getImage() : null],
                'required' => false,
                'url' => '/api/impactdata/images',
                'limit' => 1,
                'constraints' => [
                    new Constraints\Count(['max' => 1, 'min' => 0]),
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'regular-submit',
                'attr' => ['class' => 'btn btn-cyan'],
                'icon_class' => 'fa fa-save'
            ])
            ;
        
            
        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $model = $this->getModel();

        if (current($data['image']) instanceOf Image) {
            $model->setImage(current($data['image']));
            unset($data['image']);
        }

        $model->rebuildData($data, array_keys($form->all()));

        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }

}
