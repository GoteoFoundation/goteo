<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Core;

final class ModelEvents
{
    /**
     * The view.render event is thrown each time a class inherithing from Goteo\Core\Model
     * performs a "CREATE" action (new entry in database BEFORE INSERTION)
     *
     * The event listener receives an
     * Goteo\Core\Event\CreateModelEvent instance.
     *
     * @var string
     */
    const CREATE = 'model.create';

    /**
     * The view.render event is thrown each time a class inherithing from Goteo\Core\Model
     * performs a "CREATED" action (new entry in database AFTER INSERTION)
     *
     * The event listener receives an
     * Goteo\Core\Event\CreateModelEvent instance.
     *
     * @var string
     */
    const CREATED = 'model.created';

    /**
     * The view.render event is thrown each time a class inherithing from Goteo\Core\Model
     * performs a "UPDATE" action (modify entry in database BEFORE UPDATE)
     *
     * The event listener receives an
     * Goteo\Core\Event\UpdateModelEvent instance.
     *
     * @var string
     */
    const UPDATE = 'model.update';

    /**
     * The view.render event is thrown each time a class inherithing from Goteo\Core\Model
     * performs a "UPDATED" action (modify entry in database AFTER UPDATE)
     *
     * The event listener receives an
     * Goteo\Core\Event\UpdateModelEvent instance.
     *
     * @var string
     */
    const UPDATED = 'model.updated';

    /**
     * The view.render event is thrown each time a class inherithing from Goteo\Core\Model
     * performs a "DELETE" action (removes entry in database BEFORE DELETE)
     *
     * The event listener receives an
     * Goteo\Core\Event\DeleteModelEvent instance.
     *
     * @var string
     */
    const DELETE = 'model.delete';

    /**
     * The view.render event is thrown each time a class inherithing from Goteo\Core\Model
     * performs a "DELETED" action (removes entry in database AFter DELETE)
     *
     * The event listener receives an
     * Goteo\Core\Event\DeleteModelEvent instance.
     *
     * @var string
     */
    const DELETED = 'model.deleted';

}
