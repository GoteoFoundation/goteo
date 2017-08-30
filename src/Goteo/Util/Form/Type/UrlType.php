<?php

/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\UrlType as SymfonyUrlType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * This class creates overides Date to show always as the single_text option is activated
 *
 */
class UrlType extends SymfonyUrlType
{

    // /**
    //  * {@inheritdoc}
    //  */
    public function getParent()
    {
        return __NAMESPACE__.'\TextType';
    }

}
