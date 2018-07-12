<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Core\Event;

use Goteo\Core\Model;

class CreateModelEvent extends AbstractModelEvent
{
    protected $fields = [];

    public function __construct(Model $model = null, array $fields)
    {
        $this->model = $model;
        $this->fields = $fields;
    }

    public function getFields() {
        return $this->fields;
    }

}
