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

class TagsType extends TextType
{

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'label_attr' => ['class' => ''],
            'row_class' => '',
            'url' => null, // Url for autocomplete
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        if(is_array($options['attr']['class'])) $view->vars['attr']['class'] = implode(' ', $view->vars['attr']['class']);
        $view->vars['attr']['class'] .= 'form-control tagsinput';
        $view->vars['attr']['data-url'] = $options['url'];
        $view->vars['label_attr'] = $options['label_attr'];
        $view->vars['row_class'] = $options['row_class'];
    }

    public function getBlockPrefix(): string
    {
        return 'tags';
    }

}
