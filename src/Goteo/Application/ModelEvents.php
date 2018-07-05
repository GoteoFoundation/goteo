
<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application;

final class ModelEvents
{
    /**
     * The view.render event is thrown each time a class inherithing from Goteo\Core\Model
     * performs a "CREATE" action (new entry in database)
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterModelEvent instance.
     *
     * @var string
     */
    const CREATE = 'model.create';
    const UPDATE = 'model.update';
    const DELETE = 'model.delete';

}
