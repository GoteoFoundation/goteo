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
use Goteo\Application\Session;
use Goteo\Util\Form\SimpleTemplateNameParser;

use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Templating\TemplatingExtension;
use Symfony\Component\Form\Extension\Core\CoreExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper;;

class FormBuilder {
    protected $formBuilder;
    protected $viewEngine;
    protected $formFactory;

    public function __construct() {
        $csrfGenerator = new UriSafeTokenGenerator();
        $csrfStorage = new SessionTokenStorage(Session::getSession());
        $csrfManager = new CsrfTokenManager($csrfGenerator, $csrfStorage);

        // Set up the Translation component
        $translator = Lang::translator();
        $translator->addLoader('xlf', new XliffFileLoader());
        array_walk(Lang::listAll('id'), function($lang, $key) use ($translator) {
            $file1 = GOTEO_PATH . "vendor/symfony/form/Resources/translations/validators.$lang.xlf";
            $file2 = GOTEO_PATH . "vendor/symfony/validator/Resources/translations/validators.$lang.xlf";
            if(file_exists($file1)) {
                $translator->addResource('xlf', $file1, $lang, 'validators');
            }
            if(file_exists($file2)) {
                $translator->addResource('xlf', $file2, $lang, 'validators');
            }
        });

        $this->viewEngine = new PhpEngine(new SimpleTemplateNameParser(GOTEO_PATH . 'Resources/templates/forms'), new FilesystemLoader(array()));
        $this->viewEngine->addHelpers(array(new TranslatorHelper($translator)));

        $builder = new ValidatorBuilder();
        $builder->setTranslator($translator);
        $builder->setTranslationDomain('validators');
        $validator = $builder->getValidator();

        // We don't use this:
        // $this->formFactory = Forms::createFormFactoryBuilder()
        // because we need to put ExtraFieldsExtension BEFORE CoreExtension
        // so classes can be overwritten
        //
        // TODO: to service container so plugins can add extensions
        $this->formBuilder = new FormFactoryBuilder();
        $this->formBuilder
            ->addExtension(new ExtraFieldsExtension())
            ->addExtension(new CoreExtension())
            ->addExtension(new HttpFoundationExtension())
            ->addExtension(new ValidatorExtension($validator))
            ->addExtension(new TemplatingExtension($this->viewEngine, null, array(
                 GOTEO_PATH . 'vendor/symfony/framework-bundle/Resources/views/Form',
                 GOTEO_PATH . 'Resources/templates/forms/bootstrap',
            )))
            ->addExtension(new CsrfExtension($csrfManager))
        ;
    }

    public function createBuilder($defaults = null, $name = 'form', array $options = array()) {
        return $this->getFactory()->createNamedBuilder($name, FormType::class, $defaults, $options);
    }

    public function getFactory() {
        if(!$this->formFactory) {
            $this->formFactory = $this->formBuilder->getFormFactory();
        }
        return $this->formFactory;
    }

    public function getEngine() {
        return $this->viewEngine;
    }

    public function getForm() {
        return $this->viewEngine->get('form');
    }
}
