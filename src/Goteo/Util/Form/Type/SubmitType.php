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

use Symfony\Component\Form\Extension\Core\Type\SubmitType as SymfonySubmitType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * This class creates overides Date to show always as the single_text option is activated
 *
 */
class SubmitType extends SymfonySubmitType
{

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('label',  'regular-save');
        $resolver->setDefault('icon_class',  'fa fa-save');
        $resolver->setDefault('span',  '');
        $resolver->setDefault('attr',  ['class' => 'btn btn-cyan btn-lg']);
        $resolver->setDefault('row_class', '');
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['icon_class'] = $options['icon_class'];
        $view->vars['span'] = $options['span'];
        $view->vars['row_class'] = $options['row_class'];
    }

}
