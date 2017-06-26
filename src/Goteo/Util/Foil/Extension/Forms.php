<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Foil\Extension;

use Goteo\Application\App;
use Symfony\Component\HttpFoundation\Request;
use Foil\Contracts\ExtensionInterface;
use Symfony\Component\Form\FormView;

class Forms implements ExtensionInterface
{
    public function setup(array $args = [])
    {


    }

    public function provideFilters()
    {
        return [];
    }

    public function provideFunctions()
    {
        return [
          'form_form' => [$this, 'form'],
          'form_widget' => [$this, 'widget'],

        ];
    }

    public function form(FormView $formView = null)
    {
        return App::getService('app.forms')->getForm()->form($formView);
    }

    public function widget(FormView $formView = null) {
        return App::getService('app.forms')->getForm()->widget($formView);
    }


}
