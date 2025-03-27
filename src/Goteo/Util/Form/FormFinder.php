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

class FormFinder
{
    private $builder;
    private $model;

    public function setBuilder(FormBuilderInterface $builder): FormFinder
    {
        $this->builder = $builder;
        return $this;
    }

    public function getBuilder()
    {
        return $this->builder;
    }

    public function setModel($model): FormFinder
    {
        $this->model = $model;
        return $this;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getInstance(string $formClass, array $options = [])
    {
        return new $formClass($this->builder, $this->model, $options);
    }
}
