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

use Symfony\Component\Form\Extension\Core\Type\DateType as SymfonyDateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;

/**
 *
 * This class creates overides Date to show always as the single_text option is activated
 *
 */
class DatepickerType extends SymfonyDateType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        // transform to datetime if not already
        $builder->addModelTransformer(new CallbackTransformer(
            function ($date) {
                if($date instanceOf \DateTime) return $date;
                if(strpos($date, '0000-00-00') === 0) $date = null;
                elseif($date) $date = new \DateTime($date);

                return $date;
            },
            function ($date) {
                return $date;
            }
        ));
        $builder->addViewTransformer(new CallbackTransformer(
            function ($date) {
                return $date;
            },
            function ($date) {
                list($y, $m, $d) = sscanf($date, '%04d-%02d-%02d');
                if($d && $m && $y) $date = sprintf('%02d/%02d/%4d', $d, $m, $y);

                return $date;
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('widget', 'single_text');
        $resolver->setDefault('format', 'dd/MM/yyyy');
        $resolver->setDefault('row_class', '');
    }

    public function getName()
    {
        return 'datepicker';
    }


    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['row_class'] = $options['row_class'];
    }
}
