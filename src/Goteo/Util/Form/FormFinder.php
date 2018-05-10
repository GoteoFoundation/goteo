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

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use Goteo\Core\Model;

class FormFinder {
    private $builder;
    private $model;

    public function setBuilder(FormBuilderInterface $builder) {
        $this->builder = $builder;
        return $this;
    }

    public function getBuilder() {
        return $this->builder;
    }

    public function setModel(Model $model) {
        $this->model = $model;
        return $this;
    }

    public function getModel() {
        return $this->model;
    }

    public function resolve($form) {
        $class = '\Goteo\Library\Forms\Model\\' . $form . 'Form';
        if(class_exists($class)) {
            return $class;
        }
        $class = '\Goteo\Library\Forms\Admin\\' . $form . 'Form';
        if(class_exists($class)) {
            return $class;
        }
        throw new FormFinderException("$class not found");
    }

    public function getInstance($form, array $options = []) {
        $class = $this->resolve($form);
        return new $class($this->builder, $this->model, $options);
    }
}
