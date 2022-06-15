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

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultipleTypeaheadType extends CollectionType
{

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'allow_add' => true,
            'sources' => '', // Sources: 'channel', 'call', 'matcher', 'project', 'user', 'consultant'
            'label_attr' => ['class' => ''],
            'row_class' => '',
            'text' => '', // If exists, the text shown instead of the value
            'fake_id' => '', // Created automatically, the id of the typeahead input field (real data is placed in a hidden field)
            'value_field' => 'id', // Field where to extract the Value from API calls, placed in the hidden field
            'type' => 'multiple', // DO NOT CHANGE. Use the TypeaheadType if you want the 'simple' versio
        ]);
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
        $view->vars['label_attr'] = $options['label_attr'];
        $view->vars['attr']['autocomplete'] = 'off';
        $view->vars['sources'] = $options['sources'];
        $view->vars['text'] = $options['text'] ?: $view->vars['value'];
        $view->vars['row_class'] = $options['row_class'];
        $view->vars['value_field'] = $options['value_field'];
        $view->vars['type'] = $options['type'];
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'typeahead';
    }
}
