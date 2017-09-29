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

// use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * This class creates overides Date to show always as the single_text option is activated
 *
 */
class LocationType extends TextType
{

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('type', null); // user, project, call, ...
        $resolver->setDefault('item', null); // if type requires item-id (project, call, ...)
    }
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['attr']['data-geocoder-type'] = $options['type'];
        if($options['item']) {
            $view->vars['attr']['data-geocoder-item'] = $options['item'];
        }
        if(empty($view->vars['attr']['class'])) {
            $view->vars['attr']['class'] = 'form-control geo-autocomplete';
        } elseif(strpos($view->vars['attr']['class'], 'geo-autocomplete') === false) {
            $view->vars['attr']['class'] .= ' geo-autocomplete';
        }
        $view->vars['privacy_control'] = $options['type'];
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
        return 'location';
    }
}
