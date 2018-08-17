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
use Goteo\Library\Forms\Model\ProjectStoryForm;
use Goteo\Library\Forms\FormModelException;

class AdminStoryEditForm extends ProjectStoryForm {

    public function createForm() {
        $builder = $this->getBuilder();
        $options = $builder->getOptions();
        $post = $this->getModel();
        $data = $options['data'];

        parent::createForm();
        $builder
            ->add('review', 'text', [
                'label' => 'admin-stories-review',
                'constraints' => $this->getConstraints('name'),
                'disabled' => $this->getReadonly()
            ])
            ->add('image', 'dropfiles', [
                'label' => 'story-field-image',
                'disabled' => $this->getReadonly(),
                'url' => '/api/stories/images',
                'required' => true,
                'limit' => 1,
                'constraints' => [
                        new Constraints\Count(['max' => 1]),
                    ]
            ])
            ->add('pool_image', 'dropfiles', [
                'label' => 'story-field-pool-image',
                'disabled' => $this->getReadonly(),
                'url' => '/api/stories/images',
                'required' => false,
                'limit' => 1,
                'constraints' => [
                        new Constraints\Count(['max' => 1]),
                    ]

            ])
            ->add('active', 'boolean', array(
                'required' => false,
                'label' => 'admin-title-active', // Form has integrated translations
                'color' => 'cyan', // bootstrap label-* (default, success, ...)
            ))
            ->add('pool', 'boolean', array(
                'required' => false,
                'label' => 'admin-stories-pool', // Form has integrated translations
                'color' => 'cyan', // bootstrap label-* (default, success, ...)
            ))
            ;


        return $this;
    }

}
