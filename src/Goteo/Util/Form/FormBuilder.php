<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Form;

use Goteo\Application\Lang;
use Goteo\Util\Form\SimpleTemplateNameParser;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper;;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Templating\TemplatingExtension;

class FormBuilder {
    protected $viewEngine;
    protected $formFactory;

    public function __construct() {

        $this->viewEngine = new PhpEngine(new SimpleTemplateNameParser(GOTEO_PATH . 'Resources/templates/forms'), new FilesystemLoader(array()));
        $this->viewEngine->addHelpers(array(new TranslatorHelper(Lang::translator())));

        $this->formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new ExtraFieldsExtension())
            ->addExtension(new HttpFoundationExtension())
            ->addExtension(new TemplatingExtension($this->viewEngine, null, array(
             GOTEO_PATH . 'vendor/symfony/framework-bundle/Resources/views/Form',
             GOTEO_PATH . 'Resources/templates/forms/bootstrap',
        )))
        // ->addExtension(new CsrfExtension($csrfTokenManager))
        // ->addExtension(new ValidatorExtension($validator))
        ->getFormFactory();

    }


    public function createBuilder($defaults = null, $name = 'form', array $options = array()) {
        return $this->formFactory->createNamedBuilder($name, FormType::class, $defaults, $options);
    }

    public function getFactory() {
        return $this->formFactory;
    }

    public function getEngine() {
        return $this->viewEngine;
    }

    public function getForm() {
        return $this->viewEngine->get('form');
    }
}
