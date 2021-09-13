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
use Goteo\Library\Text;
use Goteo\Util\Form\Type\DropfilesType;
use Goteo\Util\Form\Type\TextareaType;
use Goteo\Util\Form\Type\TextType;
use Symfony\Component\Validator\Constraints;


class ProjectStoryForm extends AbstractFormProcessor {

    public function getConstraints($field) {
        $constraints = [];

        if($field === 'title') {
            $constraints[] = new Constraints\NotBlank();
        }
        if($field === 'description') {
            // Minimal 20 words
            $constraints[] = new Constraints\Regex([
                'pattern' => '/^\s*\S+(?:\s+\S+){19,}\s*$/',
                'message' => Text::get('validate-project-field-description')
            ]);
        }
        return $constraints;
    }

    public function createForm() {
        $builder = $this->getBuilder();
        $project = $this->getOption('project');

        $builder
            ->add('title', TextType::class, [
                'label' => 'story-field-author-organization',
                'constraints' => $this->getConstraints('name'),
                'disabled' => $this->getReadonly(),
                'attr' => ['help' => Text::get('story-tooltip-author-organization')]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'story-field-description',
                'constraints' => $this->getConstraints('description'),
                'disabled' => $this->getReadonly(),
                'required' => true,
                'attr' => ['help' => Text::get('story-tooltip-description'), 'rows' => 3]
            ])
            ->add('image', DropfilesType::class, [
                'label' => 'story-field-image',
                'disabled' => $this->getReadonly(),
                'url' => '/api/projects/' . $project->id . '/images',
                'required' => true,
                'limit' => 1,
                'constraints' => [
                        new Constraints\Count(['max' => 1, 'min' => 1]),
                    ]
            ])
            ->add('pool_image', DropfilesType::class, [
                'label' => 'story-field-pool-image',
                'disabled' => $this->getReadonly(),
                'url' => '/api/projects/' . $project->id . '/images',
                'required' => false,
                'limit' => 1,
                'constraints' => [
                    new Constraints\Count(['max' => 1]),
                ]
            ]);

        return $this;
    }

}
