<?php

namespace Goteo\Util\Forms;

use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Form\Forms;
use Goteo\Util\Forms\SimpleTemplateNameParser;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Templating\TemplatingExtension;

class FormBuilder {
    protected $viewEngine;
    protected $formFactory;

    public function __construct() {
        $path = realpath(GOTEO_PATH . 'vendor/symfony/framework-bundle/Resources/views/Form');
        // Set up the Translation component
        $translator = new Translator('en');
        $this->viewEngine = new PhpEngine(new SimpleTemplateNameParser(GOTEO_PATH . 'Resources/templates/forms'), new FilesystemLoader(array()));
        $this->viewEngine->addHelpers(array(new TranslatorHelper($translator)));

        $this->formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->addExtension(new TemplatingExtension($this->viewEngine, null, array(
            $path,
             GOTEO_PATH . 'Resources/templates/forms/bootstrap',
        )))
        // ->addExtension(new CsrfExtension($csrfTokenManager))
        // ->addExtension(new ValidatorExtension($validator))
        ->getFormFactory();

    }


    public function createBuilder($defaults = null) {
        return $this->formFactory->createBuilder(FormType::class, $defaults);
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
