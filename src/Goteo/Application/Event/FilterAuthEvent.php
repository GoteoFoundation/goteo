<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\Event;
use Goteo\Model\User;
use Goteo\Application\Session;

class FilterAuthEvent extends Event
{
    protected $user;

    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    /**
     * Returns the appropiate place to redirect a logged user
     */
    public function getUserRedirect(Request $request = null) {
        $user = $this->getUser();

        $return = '/dashboard/activity';

        if (isset($user->roles['admin']) || isset($user->roles['superadmin'])) {
            $return = '/admin';
        }

        // if return place specified
        if($request && $request->query->get('return')) {
            $return = $request->query->get('return');
        }

        if (Session::get('jumpto')) {
            return new RedirectResponse(Session::getAndDel('jumpto'));
        }


        return new RedirectResponse($return);
    }
}
