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
use Goteo\Application\Currency;
use Goteo\Model\Project\Cost;
use Goteo\Library\Forms\FormModelException;

class ProjectCostsForm extends AbstractFormProcessor implements FormProcessorInterface {
    private $costs = [];

    public function getConstraints($field) {
        $constraints = [];
        if($this->getFullValidation()) {
            $constraints[] = new Constraints\NotBlank();
        }
        elseif(strpos($field, 'description') !== 0) {
            $constraints[] = new Constraints\NotBlank();
        }
        return $constraints;
    }

    // override this to take into account costs[] array
    public function getDefaults($sanitize = true) {
        $options = $this->getBuilder()->getOptions();
        foreach($options['data']['costs'] as $cost) {
            $suffix = "_{$cost->id}";
            $options['data']["amount$suffix"] = $cost->amount;
            $options['data']["cost$suffix"] = $cost->cost;
            $options['data']["type$suffix"] = $cost->type;
            $options['data']["required$suffix"] = $cost->required;
            $options['data']["description$suffix"] = $cost->description;
        }
        if($sanitize) return array_intersect_key($options['data'], $this->getBuilder()->all());
        return $options['data'];
    }

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
                // 'constraints' => array(new Constraints\NotBlank()),
                'constraints' => $this->getConstraints("amount$suffix"),
                'required' => false,
            ])
            ->add("type$suffix", 'choice', [
                'label' => 'costs-field-type',
                'disabled' => $this->getReadonly(),
                'data' => $cost->type,
                'choices' => Cost::types(),
                'constraints' => $this->getConstraints("type$suffix"),
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
                // 'label' => 'costs-field-cost',
                'label' => 'regular-title',
                'disabled' => $this->getReadonly(),
                'data' => $cost->cost,
                'constraints' => $this->getConstraints("cost$suffix"),
                'required' => false,
            ])
            ->add("description$suffix", 'textarea', [
                'label' => 'costs-field-description',
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints("description$suffix"),
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
            ->add('title-costs', 'title', ['label' => 'costs-fields-main-title'])
            ;
        foreach($project->costs as $cost) {
            $this->addCost($cost);
        }

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();

        $data = array_intersect_key($form->getData(), $form->all());
        // print_r($data);die;
        $project = $this->getModel();
        // $project->one_round = (bool) $data['one_round'];

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
        // $validate = true;
        // foreach($ids as $id) {
        //     if($form->get("remove_$id")->isClicked()) {
        //         $this->delCost($id);
        //         $validate = false;
        //     }
        // }
        // Validate form here to avoid deleted elements
        if($validate && !$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        // Add cost
        // if($form['add-cost']->isClicked()) {
        //     $cost = new Cost(['project' => $project->id]);
        //     if(!$cost->save($errors)) {
        //         throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        //     }
        //     $this->addCost($cost);
        // }

        $project->costs = $this->costs;
        // var_dump($project->costs);die;
        if (!$project->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        if($validate && !$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }

}
