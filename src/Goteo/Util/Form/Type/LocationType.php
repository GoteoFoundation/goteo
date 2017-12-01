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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;

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
        $resolver->setDefault('compound', true);
        $resolver->setDefault('type', null); // user, project, call, ...
        $resolver->setDefault('item', null); // if type requires item-id (project, call, ...)
        $resolver->setDefault('location_object', null); // Whether to show or hide lat/lng coordinates (Has to be a LocationItem instance)
        $resolver->setDefault('location_class', 'Goteo\Model\Project\ProjectLocation'); // LocationItem implementing class
        $resolver->setDefault('location_radius', 0); // if > 0, radius will be added as an option in the map
        $resolver->setDefault('row_class', '');
        $resolver->setDefault('populate_fields', ['address', 'city', 'region', 'zipcode', 'country_code', 'country', 'latitude', 'longitude', 'radius']);
        $resolver->setDefault('always_populate', 'auto'); // If false, exact location only will be executed if empty
                                                          //'auto' will behave as false if location_object is present, true otherwise
    }

        /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        if($options['location_object'] !== null) {
            $ob = $options['location_object'];
            foreach($options['populate_fields'] as $field) {
                $val = $ob ? $ob->{$field} : '';
                if($field === 'radius') {
                    if($options['location_radius']) {
                        $val = max($val, 1);
                    } else {
                        $val = 0;
                    }
                }
                $builder->add($field, TextType::class, ['data' => $val]);
            }
            // print_r($ob->getFormatted());die;
            $builder->add('formatted_address', TextType::class, ['data' => $ob ? $ob->getFormatted() : '']);
        }
        // var_dump($options);die;
        $builder->add('location', TextType::class);

        $builder->addViewTransformer(new CallbackTransformer(
            function($loc) {
                return ['location' => $loc];
            },
            function($value) use ($options) {

                $clas = $options['location_class'];
                if($clas && $options['location_object'] !== null && $value['latitude'] && $value['longitude']) {
                    $vars = ['location' => $value['location'], 'method' => 'manual'];
                    foreach($options['populate_fields'] as $field) {
                        $vars[$field] = $value[$field];
                    }
                    $ob = new $clas($vars);
                    return $ob;
                }
                return $value['location'];
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['row_class'] = $options['row_class'];

        $view->vars['always_populate'] = $options['always_populate'];
        if($options['location_object'] !== null) {
            $view->vars['populate_fields'] = $options['populate_fields'];
            $view->vars['location_object'] = $options['location_object'];
            if($options['location_radius']) {
                $view->vars['location_radius'] = $options['location_radius'];
                // $view->vars['populate_fields'][] = 'radius';
            }
        }
    }


    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options) {
        if($options['type']) {
            $view->children['location']->vars['attr']['data-geocoder-type'] = $options['type'];
        }
        if($options['item']) {
            $view->children['location']->vars['attr']['data-geocoder-item'] = $options['item'];
        }
        if(empty($view->children['location']->vars['attr']['class'])) {
            $view->children['location']->vars['attr']['class'] = 'form-control geo-autocomplete';
        } elseif(strpos($view->children['location']->vars['attr']['class'], 'geo-autocomplete') === false) {
            $view->children['location']->vars['attr']['class'] .= ' geo-autocomplete';
        }
        $view->children['location']->vars['pre_addon'] = $view->vars['pre_addon'];
        $view->children['location']->vars['post_addon'] = $view->vars['post_addon'];
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
