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

use Goteo\Application\Session;
use Goteo\Core\Exception;
use Goteo\Library\Forms\FormProcessorInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Forms\FormModelException;
use Goteo\Model\Project\Conf;
use Goteo\Util\Form\Type\BooleanType;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\TextareaType;
use Goteo\Util\Form\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Model\User;

class ProjectCampaignForm extends AbstractFormProcessor implements FormProcessorInterface {

    public function getPhoneConstraints(): array
    {
        if ($this->getFullValidation()) {
            return [new Constraints\NotBlank()];
        } else {
            return [];
        }
    }

    public function createForm(): ProjectCampaignForm
    {
        $project = $this->getModel();
        $account = $this->getOption('account');

        $builder = $this->getBuilder();
        $admin = Session::isAdmin();
        if ($admin) {
            $builder
                ->add('type', ChoiceType::class, [
                    'label' => 'project-campaign-type-label',
                    'row_class' => 'extra',
                    'data' => $project->type ?? $project->getConfig()->getType(),
                    'choices' => $this->projectTypeChoices(),
                    'required' => false
                ])
                ->add('impact_calculator', BooleanType::class, [
                    'label' => 'project-campaign-impact-calculator',
                    'row_class' => 'extra',
                    'data' => $project->isImpactCalcActive(),
                    'attr' => [
                        'help' => Text::get('project-campaign-activate-impact-calculator')
                    ],
                    'color' => 'cyan',
                    'required' => false
                ])
                ->add('allowStripe', BooleanType::class, [
                    'label' => Text::get('project-campaign-use-stripe'),
                    'data' => $account->allow_stripe,
                    'disabled' => $this->getReadonly(),
                    'required' => false,
                    'color' => 'cyan'
                ]);
        }

        if ($admin || $project->type != Conf::TYPE_PERMANENT ) {
            $builder
                ->add('one_round', ChoiceType::class, [
                    'disabled' => $this->getReadonly(),
                    'label' => 'costs-field-select-rounds',
                    'required' => true,
                    'expanded' => true,
                    'wrap_class' => 'col-xs-6',
                    'choices' => $this->getRoundsAsChoices(),
                    'attr' => [
                        'help' => '<span class="' . ($project->one_round ? '' : 'hidden') . '">' . Text::get('tooltip-project-rounds') . '</span><span class="' . ($project->one_round ? 'hidden' : '') . '">' . Text::get('tooltip-project-2rounds') . '</span>'
                    ]
                ]);
        }

        $builder
            ->add('phone', TextType::class, [
                'label' => 'personal-field-phone',
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getPhoneConstraints(),
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-phone')]
            ])
            ->add('allowpp', BooleanType::class, [
                'label' => Text::get('project-campaign-use-paypal'),
                'data' => $account->allowpp,
                'disabled' => $this->getReadonly(),
                'required' => false,
                'color' => 'cyan',
            ])
            ->add('spread', TextareaType::class, [
                'label' => 'overview-field-spread',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => [
                    'help' => Text::get('tooltip-project-spread'),
                    'info' => '<i class="fa fa-eye-slash"></i> '. Text::get('project-non-public-field'),
                    'rows' => 8
                ]
            ]);

        return $this;
    }

    private function getRoundsAsChoices(): array
    {
        return [
            Text::get('project-one-round') => 1,
            Text::get('project-two-rounds') => 0
        ];
    }

    private function projectTypeChoices(): array
    {
        return [
            Text::get('project-campaign-type-campaign') => Conf::TYPE_CAMPAIGN,
            Text::get('project-campaign-type-permanent')=> Conf::TYPE_PERMANENT,
        ];
    }

    public function save(FormInterface $form = null, $force_save = false) {

        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $project = $this->getModel();
        $project->rebuildData($data, array_keys($form->all()));

        $errors = [];
        if (!$project->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        $data = $form->getData();
        $account = $this->getOption('account');
        $account->rebuildData([
            'allowpp' => $data['allowpp'],
            'allow_stripe' => $data['allowStripe']
        ]);

        $errors = [];
        if (!$account->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        $user = $this->getOption('user');
        if(!User::setPersonal($user, ['phone' => $data['phone']], true, $errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        $admin = Session::isAdmin();
        if ($admin) {
            try {
                $conf = Conf::get($project->id);
                if(isset($data['impact_calculator'])) {
                    if ($data['impact_calculator']) {
                        $conf->activateImpactCalculator();
                    } else {
                        $conf->deactivateImpactCalculator();
                    }
                }

                if (isset($data['type'])) {
                    $conf->setType($data['type']);
                }
                $errors = [];
                if (!$conf->save($errors)) {
                    throw new FormModelException(Text::get('form-sent-error', implode(', ', $errors)));
                }
            } catch (Exception $e) {
                throw new FormModelException($e->getMessage());
            }
        }

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }
}
