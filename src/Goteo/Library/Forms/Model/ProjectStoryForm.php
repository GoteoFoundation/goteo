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

use Goteo\Library\Forms\FormProcessorInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Symfony\Component\Validator\Constraints;
use Goteo\Model\Image;
use Goteo\Library\Text;
use Goteo\Library\Forms\FormModelException;
use Symfony\Component\Form\FormInterface;


class ProjectStoryForm extends AbstractFormProcessor implements FormProcessorInterface {

    public function getConstraints($field) {
        $constraints = [];

        if($field === 'title') {
            $constraints[] = new Constraints\NotBlank();
        }
        if($field === 'description') {
            // Minimal 30 words
            $constraints[] = new Constraints\Regex([
                'pattern' => '/^\s*\S+(?:\s+\S+){19,}\s*$/',
                'message' => Text::get('validate-project-field-description')
            ]);
        }
        if($this->getFullValidation()) {
            if(!in_array($field, ['media', 'spread'])) {
                // all fields
                $constraints[] = new Constraints\NotBlank();
            }
        }
        return $constraints;
    }

    public function createForm() {
        $builder = $this->getBuilder();

        $story = $this->getModel();

        $builder
            ->add('title', 'text', [
                'label' => 'story-field-author-organization',
                'constraints' => $this->getConstraints('name'),
                'disabled' => $this->getReadonly(),
                'attr' => ['help' => Text::get('story-tooltip-author-organization')]
            ])
            ->add('description', 'textarea', [
                'label' => 'story-field-description',
                'constraints' => $this->getConstraints('description'),
                'disabled' => $this->getReadonly(),
                'required' => true,
                'attr' => ['help' => Text::get('story-tooltip-description'), 'rows' => 3]
            ])
            ->add('image', 'dropfiles', [
                'label' => 'story-field-image',
                'constraints' => $this->getConstraints('image'),
                'disabled' => $this->getReadonly(),
                'url' => '/api/stories/' . $story->id . '/image',
                'required' => true
            ])
            ->add('pool_image', 'dropfiles', [
                'label' => 'story-field-pool-image',
                'constraints' => $this->getConstraints('pool_image'),
                'disabled' => $this->getReadonly(),
                'url' => '/api/stories/' . $story->id . '/pool-image',
                'required' => false
            ]);
          
        return $this;
    }


}
