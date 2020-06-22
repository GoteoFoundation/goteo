<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Admin;

use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;


class ChannelStoryAdminController extends AbstractAdminController
{
protected static $icon = '<i class="fa fa-2x fa-connect"></i>';

    public static function getGroup() {
        return 'channel';
    }
}
