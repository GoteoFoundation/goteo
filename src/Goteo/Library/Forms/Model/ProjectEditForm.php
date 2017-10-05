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
use Goteo\Application\Lang;
use Goteo\Model\Project;
use Goteo\Model\SocialCommitment;
use Goteo\Library\Text;
use Goteo\Library\Currency;
use Goteo\Library\Forms\FormModelException;

class ProjectEditForm extends AbstractFormProcessor implements FormProcessorInterface {

    public function createForm() {
        $currencies = Currency::listAll('name', false);
        $langs = Lang::listAll('name', false);

        $this->getBuilder()
            ->add('name', 'text', [
                'label' => 'overview-field-name',
                'constraints' => array(new Constraints\NotBlank()),
                'attr' => ['help' => Text::get('tooltip-project-name')]
            ])
            ->add('subtitle', 'text', [
                'label' => 'overview-field-subtitle',
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-subtitle')]
            ])
            ->add('lang', 'choice', [
                'label' => 'overview-field-lang',
                'choices' => $langs,
                'attr' => ['help' => Text::get('tooltip-project-lang')]
            ])
            ->add('currency', 'choice', [
                'label' => 'overview-field-currency',
                'choices' => $currencies,
                'attr' => ['help' => Text::get('tooltip-project-currency')]
            ])
            ->add('media', 'media', array(
                'label' => 'overview-field-media',
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-media')]
            ))
            ->add('description', 'markdown', [
                'label' => 'overview-field-description',
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-description'), 'rows' => 8]
            ])
            ->add('project_location', 'location', [
                'label' => 'overview-field-project_location',
                'type' => 'project',
                'item' => $this->getModel()->id,
                'required' => false,
                'pre_addon' => '<i class="fa fa-globe"></i>',
                'attr' => ['help' => Text::get('tooltip-project-project_location')]
            ])
            ->add('related', 'markdown', [
                'label' => 'overview-field-related',
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-related'), 'rows' => 8]
            ])
            ->add('spread', 'textarea', [
                'label' => 'overview-field-spread',
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-spread'), 'rows' => 8]
            ])
            ->add('extra-title', 'title', [
                'label' => 'overview-extra-fields',
                'row_class' => 'extra'
            ])
            ->add('about', 'markdown', [
                'label' => 'overview-field-about',
                'required' => false,
                'row_class' => 'extra',
                'attr' => ['help' => Text::get('tooltip-project-about'), 'rows' => 8]
            ])
            ->add('motivation', 'markdown', [
                'label' => 'overview-field-motivation',
                'required' => false,
                'row_class' => 'extra',
                'attr' => ['help' => Text::get('tooltip-project-motivation'), 'rows' => 8]
            ])
            ->add('goal', 'markdown', [
                'label' => 'overview-field-goal',
                'required' => false,
                'row_class' => 'extra',
                'attr' => ['help' => Text::get('tooltip-project-goal'), 'rows' => 8]
            ])
            ->add('scope', 'choice', [
                'label' => 'overview-field-scope',
                'required' => true,
                'wrap_class' => 'col-sm-3 col-xs-4',
                'choices' => Project::scope(),
                'expanded' => true,
                'attr' => ['help' => Text::get('tooltip-project-scope')]
            ])
            ->add('social_commitment', 'choice', [
                'label' => 'overview-field-social-category',
                'required' => true,
                // 'wrap_class' => 'col-sm-3 col-xs-4',
                'choices' => array_map(function($el){
                        return [$el->id => $el->name];
                    }, SocialCommitment::getAll()),
                'expanded' => true,
                'attr' => ['help' => Text::get('tooltip-project-social-category')]
            ])
            ->add('social_commitment_description', 'textarea', [
                'label' => 'overview-field-social-description',
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-social-description'), 'rows' => 8]
            ])
            ;
        return $this;
    }

}
