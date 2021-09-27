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

use Goteo\Util\Form\Type\EmailType;
use Goteo\Util\Form\Type\TextType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\FormInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Model\User;
use Goteo\Library\Forms\FormModelException;

class AdminUserCreateForm extends AbstractFormProcessor {

    public function getConstraints($field) {
        return [new Constraints\NotBlank()];
    }

    public function createForm() {
        $builder = $this->getBuilder();

        $builder
            ->add('email', EmailType::class, [
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('email'),
                'label' => 'regular-email',
                'pre_addon' => '<i class="fa fa-envelope"></i>',
            ])
            ->add('name', TextType::class, [
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('name'),
                'label' => 'regular-name'
            ])
            ->add('userid', TextType::class, [
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('id'),
                'label' => 'regular-id'
            ])
            ->add('password', TextType::class, [
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('password'),
                'label' => 'admin-title-password',
                'pre_addon' => '<i class="fa fa-key"></i>',
            ])
        ;
        return $this;
    }
}
