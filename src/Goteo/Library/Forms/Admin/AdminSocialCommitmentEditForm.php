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
use Goteo\Model\Footprint;
use Goteo\Library\Forms\FormModelException;

class AdminSocialCommitmentEditForm extends AbstractFormProcessor {

    public function getConstraints($field) {
        return [new Constraints\NotBlank()];
    }

    public function createForm() {
        $model = $this->getModel();
        $builder = $this->getBuilder();
        $sdgs = [];

        foreach(Sdg::getList([],0,100) as $s) {
            $sdgs['<img src="'.$s->getIcon()->getLink().'" class="icon"> '.$s->name] = $s->id;
        }
        $footprints = [];
        foreach(Footprint::getList([],0,100) as $f) {
            $footprints['<img src="'.$f->getIcon()->getLink().'" class="icon icon-3x"> '.$f->name] = $f->id;
        }

        $builder
            ->add('name', TextType::class, [
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('name'),
                'label' => 'regular-name'
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
                'url' => '/api/categories/images',
                'constraints' => array(
                    new Constraints\Count(array('max' => 1))
                ),
                'attr' => [
                    'help' => Text::get('admin-categories-if-empty-then-asset', '<img src="'.$model->getIcon(true)->getLink(64,64).'" class="icon">')
                ]
            ))
            ->add('footprints', ChoiceType::class, array(
                'label' => 'admin-title-footprints',
                'data' => array_column($model->getFootprints(), 'id'),
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choices' => $footprints,
                'choices_as_values' => true,
                'choices_label_escape' => false,
                'wrap_class' => 'col-xs-6 col-xxs-12'
            ))
            ->add('sdgs', ChoiceType::class, array(
                'label' => 'admin-title-sdgs',
                'data' => array_column($model->getSdgs(), 'id'),
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choices' => $sdgs,
                'choices_as_values' => true,
                'choices_label_escape' => false,
                'wrap_class' => 'col-xs-6 col-xxs-12'
            ))
            ;

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        // Dropfiles type always return an array, just get the first element if required
        if($data['icon'] && is_array($data['icon'])) {
            $data['icon'] = $data['icon'][0];
        } else {
            $data['icon'] = null;
        }
        $model = $this->getModel();
        $model->rebuildData($data, array_keys($form->all()));

        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        $model->replaceSdgs($data['sdgs']);
        $model->replaceFootprints($data['footprints']);

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }
}
