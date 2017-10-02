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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Library\Currency;
use Goteo\Model\Project\Cost;


class ProjectCostsForm extends AbstractFormProcessor implements FormProcessorInterface {
    private $costs = [];
    public function createForm() {
        $project = $this->getModel();
        $builder = $this->getBuilder()
            ->add('one_round', 'choice', [
                'label' => 'costs-field-select-rounds',
                'constraints' => array(new Constraints\NotBlank()),
                'required' => true,
                'expanded' => true,
                'wrap_class' => 'col-xs-6',
                'choices' => [
                    '1' => Text::get('project-one-round'),
                    '0' => Text::get('project-two-rounds')
                ],
                'attr' => ['help' => '<span class="' . ($project->one_round ? '': 'hidden') . '">' . Text::get('tooltip-project-rounds') . '</span><span class="' . ($project->one_round ? 'hidden': '') . '">' . Text::get('tooltip-project-2rounds') . '</span>']
            ])
            ->add('title-costs', 'title', ['label' => 'costs-fields-main-title'])
            ;
        foreach($project->costs as $cost) {
            $suffix = "_{$cost->id}";
            $this->costs[$cost->id] = $cost;
            $builder
                ->add("amount$suffix", 'number', [
                    'label' => 'costs-field-amount',
                    'data' => $cost->amount,
                    // 'pre_addon' => '<i class="fa fa-money"></i>',
                    'pre_addon' => Currency::get($project->currency, 'html'),
                    'post_addon' => Currency::get($project->currency, 'name'),
                    'constraints' => array(new Constraints\NotBlank()),
                    'required' => true,
                ])
                ->add("type$suffix", 'choice', [
                    'label' => 'costs-field-type',
                    'data' => $cost->type,
                    'choices' => Cost::types(),
                    'constraints' => array(new Constraints\NotBlank()),
                    'required' => true,
                ])
                ->add("cost$suffix", 'text', [
                    'label' => 'costs-field-cost',
                    'data' => $cost->cost,
                    'constraints' => array(new Constraints\NotBlank()),
                    'required' => true,
                ])
                ->add("description$suffix", 'textarea', [
                    'label' => 'costs-field-description',
                    'data' => $cost->description,
                    'required' => false,
                ]);
        }

        return $this;
    }

    public function save(FormInterface $form = null) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $project = $this->getModel();
        $project->rebuildData($data, array_keys($form->all()));

        $errors = [];
        foreach($data as $key => $val) {
            list($field, $id) = explode('_', $key);
            $cost = $this->costs[$id];
            // Check if we want to remove a translation
            // if($form->get('remove')->isClicked()) {
            //     if(!$cost->removeLang($lang)) {
            //         $errors[] = "Cost #$cost->id not deleted";
            //     }
            // }
        }

        if (!$project->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        return true;
    }

}
