<?php

namespace Goteo\Library\Forms\Model;

use Goteo\Application\Currency;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Forms\FormProcessorInterface;
use Goteo\Model\Project;
use Goteo\Model\Project\Subscription;
use Goteo\Util\Form\Type\NumberType;
use Symfony\Component\Form\Test\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class ProjectSubscriptionsForm extends AbstractFormProcessor implements FormProcessorInterface
{
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

    public function addSubscription(Subscription $subscription)
    {
        $suffix = "_{$subscription->id}";

        $this->getBuilder()
            ->add("amount$suffix", NumberType::class, [
                'label' => 'rewards-field-individual_reward-amount',
                'data' => $subscription->getAmount(),
                'type' => 'text',
                'pre_addon' => Currency::get($subscription->getProject()->currency, 'html'),
                'constraints' => [new Constraints\GreaterThan(0)],
            ]);
    }
}
