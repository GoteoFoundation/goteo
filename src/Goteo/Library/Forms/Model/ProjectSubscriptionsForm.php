<?php

namespace Goteo\Library\Forms\Model;

use Goteo\Application\Currency;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Forms\FormProcessorInterface;
use Goteo\Library\Text;
use Goteo\Model\Project;
use Goteo\Model\Project\Subscription;
use Goteo\Util\Form\Type\MarkdownType;
use Goteo\Util\Form\Type\NumberType;
use Goteo\Util\Form\Type\SubmitType;
use Goteo\Util\Form\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints;

class ProjectSubscriptionsForm extends AbstractFormProcessor implements FormProcessorInterface
{
    /** @var Subscription[] */
    private array $subscriptions;

    public function createForm()
    {
        $builder = $this->getBuilder();

        /** @var Project */
        $project = $this->getModel();

        /** @var Subscription[] */
        $subscriptions = Subscription::getAll($project);

        foreach ($subscriptions as $subscription) {
            $this->addSubscription($subscription);
        }

        return $this;
    }

    public function save(?FormInterface $form = null, $force_save = false)
    {
        if (!$form) $form = $this->getBuilder()->getForm();

        $data = array_intersect_key($form->getData(), $form->all());

        /** @var Project */
        $project = $this->getModel();
        $project->one_round = (bool) $data['one_round'];

        $errors = [];
        $ids = [];

        foreach ($data as $key => $val) {
            list($field, $id) = explode('_', $key);
            if (!in_array($field, ['amount', 'name', 'description'])) continue;
            $ids[$id] = $id;

            $subscription = $this->subscriptions[$id];
            $subscription->{$field} = $val;
        }

        // Check if we want to remove a subscription
        $validate = true;

        if ($validate && !$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $project->subscriptions = $this->subscriptions;

        if (!$project->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ', $errors)));
        }

        if ($validate && !$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }

    public function addSubscription(Subscription $subscription)
    {
        $this->subscriptions[$subscription->id] = $subscription;

        /** @var Project */
        $project = $this->getModel();

        $suffix = "_{$subscription->id}";

        $this->getBuilder()
            ->add("amount$suffix", NumberType::class, [
                'label' => 'rewards-field-individual_reward-amount',
                'data' => $subscription->getAmount(),
                'type' => 'text',
                'pre_addon' => Currency::get($subscription->getProject()->currency, 'html'),
                'constraints' => [new Constraints\GreaterThan(0)],
            ])
            ->add("name$suffix", TextType::class, [
                'label' => 'regular-title',
                'data' => $subscription->name,
                'constraints' => [new Constraints\NotBlank()],
                'required' => false,
            ])
            ->add("description$suffix", MarkdownType::class, [
                'label' => 'rewards-field-individual_reward-description',
                'data' => $subscription->description,
                'constraints' => [new Constraints\NotBlank()],
                'required' => false,
                'attr' => [
                    'data-image-upload' => '/api/projects/' . $project->id . '/images',
                    'help' => Text::get('tooltip-drag-and-drop-images'),
                    'rows' => 4,
                    'data-toolbar' => 'close,bold,italic,link,unordered-list,ordered-list,preview,fullscreen,guide'
                ]
            ])
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
}
