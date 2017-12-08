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
use Goteo\Core\Model;

interface FormProcessorInterface {
    public function createForm();
    public function save(FormInterface $form = null, $force_save = false);
    public function setBuilder(FormBuilderInterface $builder);
    public function getBuilder();
    public function getDefaults($sanitize = true);
    public function getForm();
    public function setModel(Model $model);
    public function getModel();
    public function setReadonly($readonly);
    public function getReadonly();
    public function setFullValidation($full_validation, $show_errors = false);
    public function getFullValidation();
    public function showErrors();
}
