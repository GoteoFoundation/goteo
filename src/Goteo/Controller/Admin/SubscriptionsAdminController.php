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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionsAdminController extends AbstractAdminController
{
    public static string $label = 'admin-subscriptions';

    public static function getGroup(): string
    {
        return 'activity';
    }

    public function indexAction(Request $request): Response
    {
        return new Response('test', 200);
    }
}
