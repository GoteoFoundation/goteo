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
use Goteo\Library\Forms\FormModelException;

class ProjectCostsForm extends AbstractFormProcessor implements FormProcessorInterface {
    private $costs = [];

    public function delCost($id) {
        unset($this->costs[$id]);
        $this->getBuilder()
            ->remove("amount_$id")
            ->remove("type_$id")
            ->remove("required_$id")
            ->remove("cost_$id")
            ->remove("description_$id")
            ->remove("remove_$id")
            ;
    }

    public function addCost(Cost $cost) {
        $project = $this->getModel();
        $this->costs[$cost->id] = $cost;
        $suffix = "_{$cost->id}";
        $this->getBuilder()
            ->add("amount$suffix", 'number', [
                'label' => 'costs-field-amount',
                'disabled' => $this->getReadonly(),
                'data' => $cost->amount,
                // 'pre_addon' => '<i class="fa fa-money"></i>',
                'pre_addon' => Currency::get($project->currency, 'html'),
                // 'post_addon' => Currency::get($project->currency, 'name'),
                'constraints' => array(new Constraints\NotBlank()),
                'required' => false,
            ])
            ->add("type$suffix", 'choice', [
                'label' => 'costs-field-type',
                'disabled' => $this->getReadonly(),
                'data' => $cost->type,
                'choices' => Cost::types(),
                'constraints' => array(new Constraints\NotBlank()),
                'required' => true,
            ])
            ->add("required$suffix", 'choice', [
                'label' => 'costs-field-required_cost',
                'disabled' => $this->getReadonly(),
                'data' => (int)$cost->required,
                'choices' => [
                    '1' => Text::get('costs-field-required_cost-yes'),
                    '0' => Text::get('costs-field-required_cost-no')
                ],
                'required' => true,
            ])
            ->add("cost$suffix", 'text', [
                'label' => 'costs-field-cost',
                'disabled' => $this->getReadonly(),
                'data' => $cost->cost,
                'constraints' => array(new Constraints\NotBlank()),
                'required' => false,
            ])
            ->add("description$suffix", 'textarea', [
                'label' => 'costs-field-description',
                'disabled' => $this->getReadonly(),
                'data' => $cost->description,
                'required' => false,
            ]);
        if(!$this->getReadonly()) {
            $this->getBuilder()
                ->add("remove$suffix", 'submit', [
                    'label' => Text::get('regular-delete'),
                    'icon_class' => 'fa fa-trash',
                    'span' => 'hidden-xs',
                    'attr' => [
                        'class' => 'pull-right btn btn-default remove-cost',
                        'data-confirm' => Text::get('project-remove-cost-confirm')
                        ]
                ]);
        }
    }

    public function createForm() {
        $project = $this->getModel();
        $builder = $this->getBuilder()
            ->add('one_round', 'choice', [
                'disabled' => $this->getReadonly(),
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
            $this->addCost($cost);
        }

        return $this;
    }

    public function save(FormInterface $form = null) {
        if(!$form) $form = $this->getBuilder()->getForm();

        $data = array_intersect_key($form->getData(), $form->all());
        // print_r($data);die;
        $project = $this->getModel();
        $project->one_round = (bool) $data['one_round'];

        $errors = [];
        $ids = [];

        foreach($data as $key => $val) {
            list($field, $id) = explode('_', $key);
            if(!in_array($field, ['amount', 'type', 'required', 'cost', 'description'])) continue;
            $ids[$id] = $id;

            $cost = $this->costs[$id];
            $cost->{$field} = $val;
        }

        // Check if we want to remove a cost
        $validate = true;
        foreach($ids as $id) {
            if($form->get("remove_$id")->isClicked()) {
                $this->delCost($id);
                $validate = false;
            }
        }

        // Validate form here to avoid deleted elements
        if($validate && !$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        // Add cost
        if($form['add-cost']->isClicked()) {
            $cost = new Cost(['project' => $project->id]);
            if(!$cost->save($errors)) {
                throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
            }
            $this->addCost($cost);
        }

        $project->costs = $this->costs;
        // var_dump($project->costs);die;
        if (!$project->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        return $this;
    }

}
