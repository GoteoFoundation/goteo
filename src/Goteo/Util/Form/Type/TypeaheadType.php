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
class TypeaheadType extends TextType
{

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('sources', ''); // Sources: 'channel', 'call', 'matcher', 'project', 'user', 'consultant'
        // $resolver->setDefault('engines', []); // Url for autocomplete
        // $resolver->setDefault('defaults', []); // Url for autocomplete
        $resolver->setDefault('row_class', '');
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        if(is_array($options['attr']['class'])) $view->vars['attr']['class'] = implode(' ', $view->vars['attr']['class']);
        $view->vars['attr']['class'] .= 'form-control typeahead';
        $view->vars['sources'] = $options['sources'];
        $view->vars['row_class'] = $options['row_class'];
    }

    public function getName()
    {
        return 'typeahead';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'typeahead';
    }
}
