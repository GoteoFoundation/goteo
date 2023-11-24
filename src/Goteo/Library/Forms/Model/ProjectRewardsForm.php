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

use Goteo\Util\Form\Type\BooleanType;
use Goteo\Util\Form\Type\MarkdownType;
use Goteo\Util\Form\Type\NumberType;
use Goteo\Util\Form\Type\SubmitType;
use Goteo\Util\Form\Type\TextType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Goteo\Library\Forms\FormProcessorInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Application\Currency;
use Goteo\Application\Session;
use Goteo\Model\Project\Reward;
use Goteo\Library\Forms\FormModelException;
use Goteo\Model\Project;
use Goteo\Model\Project\Account;

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
        $this->rewards[$reward->id] = $reward;
        $suffix = "_{$reward->id}";

        /** @var Project */
        $project = $this->getModel();

        // readonly only if has no invests associated
        $units_readonly = $readonly = $this->getReadonly() && !$reward->isDraft() && $reward->getTaken();
        $subs_readonly = !Account::getAllowStripe($project->id);
        $remove_readonly = $this->getReadonly()&&$reward->getTaken();
        // Readonly allows edit number of rewards if project in campaign
        if($project->inCampaign()) {
            $units_readonly = false;
        }

        $this->getBuilder()
            ->add("amount$suffix", NumberType::class, [
                'label' => 'rewards-field-individual_reward-amount',
                'data' => $reward->amount,
                'type' => 'text',
                'disabled' => $readonly,
                'pre_addon' => Currency::get($project->currency, 'html'),
                'constraints' => $this->getConstraints("amount$suffix"),
                'required' => false,
            ])
            ->add("units$suffix", NumberType::class, [
                'label' => 'rewards-field-individual_reward-units',
                'data' => (int)$reward->units,
                'type' => 'text',
                'disabled' => $units_readonly,
                'pre_addon' => '#',
                'constraints' => $this->getConstraints("units$suffix"),
                'required' => false,
            ])
            ->add("unlimited$suffix", BooleanType::class, [
                'label' => false,
                'data' => (int)$reward->units === 0,
                'disabled' => $units_readonly,
                'required' => false,
                'color' => 'cyan'
            ])
            ->add("reward$suffix", TextType::class, [
                'label' => 'regular-title',
                'data' => $reward->reward,
                'disabled' => $readonly,
                'constraints' => $this->getConstraints("reward$suffix"),
                'required' => false,
            ])
            ->add("description$suffix", MarkdownType::class, [
                'label' => 'rewards-field-individual_reward-description',
                'disabled' => $readonly,
                'data' => $reward->description,
                'constraints' => $this->getConstraints("description$suffix"),
                'required' => false,
                'attr'=> [
                    'data-image-upload' => '/api/projects/' . $project->id . '/images',
                    'help' => Text::get('tooltip-drag-and-drop-images'),
                    'rows' => 4,
                    'data-toolbar' => 'close,bold,italic,link,unordered-list,ordered-list,preview,fullscreen,guide'
                ]

            ]);

        if(!$remove_readonly) {
            $this->getBuilder()
                ->add("remove$suffix", SubmitType::class, [
                    'label' => Text::get('regular-delete'),
                    'icon_class' => 'fa fa-trash',
                    'span' => 'hidden-xs',
                    'attr' => [
                        'class' => 'pull-right btn btn-default remove-reward',
                        'data-confirm' => Text::get('project-remove-reward-confirm')
                        ]
                ]);
        }

        if (Session::isAdmin()) {
            $this->getBuilder()->add("subscribable$suffix", BooleanType::class, [
                'label' => false,
                'data' => $reward->subscribable,
                'disabled' => $subs_readonly,
                'required' => false,
                'color' => 'cyan'
            ]);
        }
    }

    public function createForm() {
        $project = $this->getModel();
        $builder = $this->getBuilder();
        foreach($project->individual_rewards as $reward) {
            $this->addReward($reward);
        }

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();

        $data = array_intersect_key($form->getData(), $form->all());
        $project = $this->getModel();
        $project->one_round = (bool) $data['one_round'];

        $errors = [];
        $ids = [];

        foreach($data as $key => $val) {
            list($field, $id) = explode('_', $key);
            if(!in_array($field, ['amount', 'icon', 'units', 'reward', 'description', 'subscribable'])) continue;
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

        if($validate && !$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $project->individual_rewards = $this->rewards;
        if (!$project->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }
        if($validate && !$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }

}
