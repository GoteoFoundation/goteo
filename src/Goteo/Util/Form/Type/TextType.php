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

use Symfony\Component\Form\Extension\Core\Type\TextType as SymfonyTextType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * This class creates overides Date to show always as the single_text option is activated
 *
 */
class TextType extends SymfonyTextType
{

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('pre_addon', '');
        $resolver->setDefault('row_class', '');
        $resolver->setDefault('post_addon', '');
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['row_class'] = $options['row_class'];
        $view->vars['pre_addon'] = $options['pre_addon'];
        $view->vars['post_addon'] = $options['post_addon'];
    }

    // /**
    //  * {@inheritdoc}
    //  */
    // public function getParent()
    // {
    //     return __NAMESPACE__.'\TextType';
    // }

}
