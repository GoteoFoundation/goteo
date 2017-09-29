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
use Goteo\Library\Text;
use Goteo\Library\Currency;


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
            ->add('description', 'textarea', [
                'label' => 'overview-field-description',
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-description')]
            ])
            ->add('project_location', 'location', [
                'label' => 'overview-field-project_location',
                'type' => 'project',
                'item' => $this->getModel()->id,
                'required' => false,
                'pre_addon' => '<i class="fa fa-globe"></i>',
                'attr' => ['help' => Text::get('tooltip-project-project_location')]
            ]);
        return $this;
    }

}
