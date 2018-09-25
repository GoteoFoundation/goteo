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
use Goteo\Model\SocialCommitment;
use Goteo\Library\Forms\FormModelException;

class AdminCategoryEditForm extends AbstractFormProcessor {

    public function getConstraints($field) {
        return [new Constraints\NotBlank()];
    }

    public function createForm() {
        $user = $this->getModel();

        $builder = $this->getBuilder();
        $options = $builder->getOptions();
        $defaults = $options['data'];

        $social_commitments = [];
        foreach(SocialCommitment::getAll() as $s) {
            $social_commitments[$s->id] = $s->name;
        }

        // print_r($defaults);die;
        $builder
            ->add('name', 'text', [
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('name'),
                'label' => 'regular-name'
            ])
            ->add('description', 'text', [
                'disabled' => $this->getReadonly(),
                'required' => false,
                'label' => 'regular-description'
            ])
            ->add('social_commitment', 'choice', array(
                'label' => 'admin-title-social_commitment',
                'required' => false,
                'choices' => $social_commitments
            ));

        ;
        return $this;
    }
}
