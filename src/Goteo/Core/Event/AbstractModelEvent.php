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

use Symfony\Contracts\EventDispatcher\Event;
use Goteo\Core\Model;

abstract class AbstractModelEvent extends Event
{
    protected $model;

    public function __construct(Model $model = null)
    {
        $this->model = $model;
    }

    public function getModel() {
        return $this->model;
    }

    public function is($model) {
        if($this->model) {
            return $this->model->getTable() === strtolower($model);
        }
        return false;
    }
}
