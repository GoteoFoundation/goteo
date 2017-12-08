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

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;

/**
 *
 * This class creates overides Date to show always as the single_text option is activated
 *
 */
class BooleanType extends CheckboxType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // transform to datetime if not already
        $builder->addViewTransformer(new CallbackTransformer(
            function ($bool) {
                return (bool) $bool;
            },
            function ($bool) {
                return $bool;
            }
        ));
        parent::buildForm($builder, $options);
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('color', 'default'); // default, success, danger, warning...
        $resolver->setDefault('label_position', 'right');
        $resolver->setDefault('row_class', '');
        $resolver->setDefault('label_attr',  ['class' => 'control-label']);
        $resolver->setDefault('no_input_wrap', true);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        // $view->vars['row_class'] = 'material-switch';
        // $view->vars['label_position'] = 'none';
        $view->vars['color'] = $options['color'];
        $view->vars['no_input_wrap'] = $options['no_input_wrap'];
        $view->vars['label_position'] = $options['label_position'];
        $view->vars['label_attr'] = $options['label_attr'];
        $view->vars['row_class'] = $options['row_class'];
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'boolean';
    }

}
