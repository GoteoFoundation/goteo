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

use Goteo\Util\Form\Type\SubmitType;
use Goteo\Library\Forms\FormProcessorInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Text;

class UserSubscriptionForm extends AbstractFormProcessor implements FormProcessorInterface
{

    public function createForm()
    {
        $builder = $this->getBuilder();
        $builder->add("remove", SubmitType::class, [
            'label' => Text::get('regular-cancel'),
            'icon_class' => 'fa fa-ban',
            'attr' => [
                'class' => 'pull-right btn btn-default cancel-subscription',
                'data-confirm' => Text::get('subscription-cancel-confirm')
            ]
        ]);

        return $this;
    }
}
