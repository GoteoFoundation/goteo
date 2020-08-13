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
        $resolver->setDefault('text', ''); // If exists, the text shown instead of the value
        $resolver->setDefault('fake_id', ''); // Created automatically, the id of the typeahead input field (real data is placed in a hidden field)
        $resolver->setDefault('value_field', 'id'); // Field where to extract the Value from API calls, placed in the hidden field
        $resolver->setDefault('type','simple'); // Field to change between typeahead with only one input or with multiple ['simple', 'multiple']
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        if(is_array($options['attr']['class'])) $view->vars['attr']['class'] = implode(' ', $view->vars['attr']['class']);
        if(empty($view->vars['fake_id'])) $view->vars['fake_id'] = 'typeahead_' . $view->vars['id'];
        $view->vars['attr']['class'] .= 'form-control typeahead';
        $view->vars['attr']['autocomplete'] = 'off';
        $view->vars['sources'] = $options['sources'];
        $view->vars['text'] = $options['text'] ? $options['text'] : $view->vars['value'];
        $view->vars['row_class'] = $options['row_class'];
        $view->vars['value_field'] = $options['value_field'];
        $view->vars['type'] = $options['type'];
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
