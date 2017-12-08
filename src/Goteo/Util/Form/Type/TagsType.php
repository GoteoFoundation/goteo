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

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * This class creates overides Date to show always as the single_text option is activated
 *
 */
class TagsType extends TextType
{

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('url', null); // Url for autocomplete
        $resolver->setDefault('row_class', '');
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        if(!is_array($options['attr']['class'])) $view->vars['attr']['class'] = [];
        $view->vars['attr']['class'] .= 'form-control tagsinput';
        $view->vars['attr']['data-url'] = $options['url'];
        $view->vars['row_class'] = $options['row_class'];
    }

    public function getName()
    {
        return 'tags';
    }

}
