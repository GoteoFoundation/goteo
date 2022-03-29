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
use Goteo\Util\Form\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Model\SocialCommitment;
use Goteo\Model\Sdg;
use Goteo\Model\Footprint;
use Goteo\Library\Forms\FormModelException;

class AdminCategoryEditForm extends AbstractFormProcessor {

    public function getConstraints() {
        return [new Constraints\NotBlank()];
    }

    public function createForm() {
        $model = $this->getModel();

        $builder = $this->getBuilder();

        $social_commitments = [];
        foreach(SocialCommitment::getAll() as $s) {
            $social_commitments[$s->name] = $s->id;
        }
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
                'constraints' => $this->getConstraints(),
                'label' => 'regular-name'
            ])
            ->add('description', TextType::class, [
                'disabled' => $this->getReadonly(),
                'required' => false,
                'label' => 'regular-description'
            ])
            ->add('social_commitment', ChoiceType::class, array(
                'label' => 'admin-title-social_commitment',
                'required' => false,
                'choices' => $social_commitments
            ))
            ->add('footprints', ChoiceType::class, array(
                'label' => 'admin-title-footprints',
                'data' => array_column($model->getFootprints(), 'id'),
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choices' => $footprints,
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
