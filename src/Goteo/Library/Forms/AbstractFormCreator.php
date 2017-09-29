<?php

/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library\Forms;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use Goteo\Core\Model;


abstract class AbstractFormCreator implements FormCreatorInterface {
    private $builder;
    private $model;
    private $options;

    public function __construct(FormBuilderInterface $builder, Model $model, array $options = []) {
        $this->setBuilder($builder);
        $this->setModel($model);
        $this->setOptions($options);
    }

    /**
     * Classes should add form fields here
     */
    public function createForm() {
        return $this;
    }

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

    public function setOptions(array $options) {
        $this->options = $options;
        return $this;
    }

    public function getOptions() {
        return $this->options;
    }

    public function getOption($key) {
        return $this->options[$key];
    }
}
