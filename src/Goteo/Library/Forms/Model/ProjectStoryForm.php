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
                'disabled' => $this->getReadonly(),
                'url' => '/api/projects/' . $project->id . '/images',
                'required' => true,
                'limit' => 1
            ])
            ->add('pool_image', 'dropfiles', [
                'label' => 'story-field-pool-image',
                'disabled' => $this->getReadonly(),
                'url' => '/api/projects/' . $project->id . '/images',
                'required' => false,
                'limit' => 1
            ]);

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {

        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) {
            throw new FormModelException(Text::get('form-has-errors'));
        }

        $data = $form->getData();
        $model = $this->getModel();
        
        if ($data['image'] && is_array($data['image'])) {
            if ($data['image']['removed'] && $model->image == current($data['image']['removed'])->id)
                $model->image = null;

            if ($data['image']['uploads'] && is_array($data['image']['uploads']))
                $model->image = $data['image']['uploads'][0];
        }

        if ($data['pool_image'] && is_array($data['pool_image'])) {
            if ($data['pool_image']['removed'] && $model->pool_image == current($data['pool_image']['removed'])->id)
                $model->pool_image = null;

            if ($data['pool_image']['uploads'] && is_array($data['pool_image']['uploads']))
                $model->pool_image = $data['pool_image']['uploads'][0];
        }

        unset($data['image']);
        unset($data['pool_image']);
        $model->rebuildData($data, array_keys($form->all()));
        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }
        return $this;
    }
}
