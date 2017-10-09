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

use Goteo\Application\App;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
// use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use League\HTMLToMarkdown\HtmlConverter;

/**
 *
 * This class creates a Symfony Form Type for Markdown editing (needs assets/js/forms.js)
 *
 */
class MarkdownType extends TextareaType
{

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if($view->vars['disabled']) {
            $view->vars['value'] = App::getService('app.md.parser')->text($view->vars['value']);
        }
        elseif(preg_match('/<br|<p|<a/i', $view->vars['value'])) {
        // TODO: check backward compatibility of this
            $converter = new HtmlConverter();
            $view->vars['value'] = $converter->convert($view->vars['value']);
        }
        $view->vars['type'] = 'markdown';
        $view->vars['row_class'] = $options['row_class'];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'markdown';
    }
}
