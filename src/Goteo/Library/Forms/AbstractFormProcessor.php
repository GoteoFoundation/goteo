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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints;
use Goteo\Core\Model;
use Goteo\Library\Text;


abstract class AbstractFormProcessor implements FormProcessorInterface {
    private $builder;
    private $model;
    private $readonly = false;
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

    public function setReadonly($readonly) {
        $this->readonly = $readonly;
        return $this;
    }

    public function getReadonly() {
        return $this->readonly;
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

    public function save(FormInterface $form = null) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $model = $this->getModel();
        $model->rebuildData($data, array_keys($form->all()));

        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        return $this;
    }
}
