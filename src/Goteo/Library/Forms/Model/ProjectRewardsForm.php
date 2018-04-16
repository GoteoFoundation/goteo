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

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Goteo\Library\Forms\FormProcessorInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Application\Currency;
use Goteo\Model\Project\Reward;
use Goteo\Library\Forms\FormModelException;

class ProjectRewardsForm extends AbstractFormProcessor implements FormProcessorInterface {
    private $rewards = [];

    public function getConstraints($field) {
        $constraints = [];
        if(strpos($field, 'units') === 0) {
            $constraints[] = new Constraints\Callback(function($object, ExecutionContextInterface $context) use ($field){
                list($key, $id) = explode('_', $field);
                $reward = $this->rewards[$id];
                $taken = $reward ? $reward->getTaken() : 0;
                if($object && $taken > $object) {
                    $context->buildViolation(Text::get('project-validation-error-rewards_units', $taken))
                    ->atPath($field)
                    ->addViolation();
                }
            });
        }
        if(strpos($field, 'amount') === 0) {
            $constraints[] = new Constraints\GreaterThan(0);
        }
        elseif($this->getFullValidation()) {
            $constraints[] = new Constraints\NotBlank();
        }
        elseif(strpos($field, 'description') !== 0) {
            $constraints[] = new Constraints\NotBlank();
        }
        return $constraints;
    }

    // override this to take into account rewards[] array
    public function getDefaults($sanitize = true) {
        $options = $this->getBuilder()->getOptions();
        foreach($options['data']['individual_rewards'] as $reward) {
            $suffix = "_{$reward->id}";
            $options['data']["amount$suffix"] = $reward->amount;
            // $options['data']["icon$suffix"] = $reward->icon;
            $options['data']["units$suffix"] = $reward->units;
            $options['data']["reward$suffix"] = $reward->reward;
            $options['data']["description$suffix"] = $reward->description;
        }
        // print_r($options['data']);die;
        if($sanitize) return array_intersect_key($options['data'], $this->getBuilder()->all());
        return $options['data'];
    }

    public function delReward($id) {
        if($this->getReadonly() && !$this->rewards[$id]->isDraft()) {
            return false;
        }
        unset($this->rewards[$id]);
        $this->getBuilder()
            ->remove("amount_$id")
            // ->remove("icon_$id")
            ->remove("reward_$id")
            ->remove("units_$id")
            ->remove("description_$id")
            ->remove("remove_$id")
            ;
    }

    public function addReward(Reward $reward) {
        $project = $this->getModel();
        $this->rewards[$reward->id] = $reward;
        $project = $this->getModel();
        $suffix = "_{$reward->id}";
        // readonly only if has no invests associated
        $units_readonly = $readonly = $this->getReadonly() && !$reward->isDraft();
        // Readonly allows edit number of rewards if project in campaign
        if($project->inCampaign()) {
            $units_readonly = false;
        }

        $this->getBuilder()
            ->add("amount$suffix", 'number', [
                'label' => 'rewards-field-individual_reward-amount',
                'data' => $reward->amount,
                'disabled' => $readonly,
                // 'pre_addon' => '<i class="fa fa-money"></i>',
                'pre_addon' => Currency::get($project->currency, 'html'),
                // 'post_addon' => Currency::get($project->currency, 'name'),
                'constraints' => $this->getConstraints("amount$suffix"),
                'required' => false,
            ])
            ->add("units$suffix", 'number', [
                'label' => 'rewards-field-individual_reward-units',
                'data' => (int)$reward->units,
                'disabled' => $units_readonly,
                'pre_addon' => '#',
                'constraints' => $this->getConstraints("units$suffix"),
                'required' => false,
            ])
            ->add("unlimited$suffix", 'boolean', [
                'label' => false,
                'data' => (int)$reward->units === 0,
                'disabled' => $units_readonly,
                'required' => false,
                'color' => 'cyan'
            ])
            // ->add("icon$suffix", 'choice', [
            //     'label' => 'rewards-field-icon',
            //     'data' => $reward->icon,
            //     'choices' => Reward::icons('individual'),
            //     'constraints' => array(new Constraints\NotBlank()),
            //     'required' => true,
            // ])
            ->add("reward$suffix", 'text', [
                // 'label' => 'rewards-field-individual_reward-reward',
                'label' => 'regular-title',
                'data' => $reward->reward,
                'disabled' => $readonly,
                'constraints' => $this->getConstraints("reward$suffix"),
                'required' => false,
            ])
            ->add("description$suffix", 'textarea', [
                'label' => 'rewards-field-individual_reward-description',
                'disabled' => $readonly,
                'data' => $reward->description,
                'constraints' => $this->getConstraints("description$suffix"),
                'required' => false,
            ]);
        if(!$readonly) {
            $this->getBuilder()
                ->add("remove$suffix", 'submit', [
                    'label' => Text::get('regular-delete'),
                    'icon_class' => 'fa fa-trash',
                    'span' => 'hidden-xs',
                    'attr' => [
                        'class' => 'pull-right btn btn-default remove-reward',
                        'data-confirm' => Text::get('project-remove-reward-confirm')
                        ]
                ]);
        }
    }

    public function createForm() {
        $project = $this->getModel();
        $builder = $this->getBuilder()
            // ->add('title-rewards', 'title', ['label' => 'rewards-fields-individual_reward-title'])
            ;
        foreach($project->individual_rewards as $reward) {
            $this->addReward($reward);
        }

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();

        $data = array_intersect_key($form->getData(), $form->all());
        // print_r($data);die;
        $project = $this->getModel();
        $project->one_round = (bool) $data['one_round'];

        $errors = [];
        $ids = [];

        foreach($data as $key => $val) {
            list($field, $id) = explode('_', $key);
            if(!in_array($field, ['amount', 'icon', 'units', 'reward', 'description'])) continue;
            if($field == 'units' && $data['unlimited_' . $id]) {
                $val = 0;
            }
            $ids[$id] = $id;

            $reward = $this->rewards[$id];
            $taken = $reward ? $reward->getTaken() : 0;
            $reward->{$field} = $val;
            if($field == 'units') {
                if($val && $val < $taken) {
                    throw new FormModelException(Text::get('form-has-errors'));
                }
            }
        }

        // Check if we want to remove a reward
        $validate = true;
        // foreach($ids as $id) {
        //     if($form->get("remove_$id")->isClicked()) {
        //         $this->delReward($id);
        //         $validate = false;
        //     }
        // }

        // Validate form here to avoid deleted elements
        if($validate && !$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        // Add reward
        // if($form['add-reward']->isClicked() && (!$this->getReadonly() || $project->isAlive())) {
        //     $reward = new Reward(['project' => $project->id, 'type' => 'individual']);
        //     if(!$reward->save($errors)) {
        //         throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        //     }
        //     $this->addReward($reward);
        // }

        $project->individual_rewards = $this->rewards;
        // var_dump($project->rewards);die;
        if (!$project->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }
        if($validate && !$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }

}
