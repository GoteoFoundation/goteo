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
use Goteo\Model\User;
use Goteo\Library\Forms\Model\ProjectPostForm;
use Goteo\Library\Forms\FormModelException;

class AdminPostEditForm extends ProjectPostForm {

    public function createForm() {
        parent::createForm();
        $builder = $this->getBuilder();
        $post = $this->getModel();

        // Replace markdown by html editor if type
        if($post->type === 'html') {
            $builder->add('text', 'textarea', array(
                'label' => 'regular-text',
                'required' => false,
                'html_editor' => true
                // 'constraints' => array(new Constraints\NotBlank()),
            ));

            //     ->add('text', 'markdown', array(
            //     'label' => 'regular-text',
            //     'required' => false,
            //     // 'constraints' => array(new Constraints\NotBlank()),
            // ))

        }


        return $this;
    }

}
