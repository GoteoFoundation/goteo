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
    protected $builder;
    protected $form;
    protected $model;
    protected $readonly = false;
    protected $options;
    protected $full_validation = false;
    protected $show_errors = false;

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

    public function getDefaults($sanitize = true) {
        $options = $this->builder->getOptions();
        $data = $options['data'];
        if($sanitize) $data = array_intersect_key($data, $this->builder->all());
        // var_dump($data);die;
        // print_r(array_keys($data));
        return $data;
    }

    public function getForm() {
        if($this->form) return $this->form;
        $this->form = $this->builder->getForm();
        if($this->showErrors()) {
            // var_dump($this->getDefaults(true));die;
            // print_r(array_keys($this->form->all()));
            $this->form->submit($this->getDefaults(true), false);
        }
        return $this->form;
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

    public function setFullValidation($full_validation, $show_errors = false) {
        $this->full_validation = $full_validation;
        $this->show_errors = $show_errors;
        return $this;
    }

    public function getFullValidation() {
        return $this->full_validation;
    }

    public function showErrors() {
        return $this->show_errors;
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

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $model = $this->getModel();
        $model->rebuildData($data, array_keys($form->all()));

        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }
}
