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

use Goteo\Application\Session;
use Goteo\Application\Message;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;
use Goteo\Library\Text;
use Goteo\Model\User;
use Goteo\Library\Forms\FormModelException;

class UsersAdminController extends AbstractAdminController {
    protected static $icon = '<i class="fa fa-2x fa-users"></i>';

    public static function getRoutes() {
        return [
            new Route(
                '/',
                ['_controller' => __CLASS__ . "::listAction"]
            ),
            new Route(
                '/roles/{uid}', [
                    '_controller' => __CLASS__ . "::rolesAction",
                    'uid' => null
                ]
            ),
            new Route(
                '/impersonate/{uid}',
                ['_controller' => __CLASS__ . "::impersonateAction"]
            ),
            new Route(
                '/manage/{uid}',
                ['_controller' => __CLASS__ . "::manageAction"]
            ),
            new Route(
                '/add',
                ['_controller' => __CLASS__ . "::addAction"]
            )
        ];
    }

    // Only need to be defined if we want a custom group for this module
    // public static function getSidebar() {
    //     return [
    //         '/users' => Text::get('admin-users'),
    //     ];
    // }


    public function listAction(Request $request) {
        $filters = ['superglobal' => $request->query->get('q')];
        $limit = 25;
        $page = $request->query->get('pag') ?: 0;
        $users = User::getList($filters, [], $page * $limit, $limit);
        $total = User::getList($filters, [], 0, 0, true);

        return $this->viewResponse('admin/users/list', [
            'list' => $users,
            'link_prefix' => '/users/manage/',
            'total' => $total,
            'limit' => $limit,
            'filter' => [
                '_action' => '/users',
                'q' => Text::get('admin-users-global-search')
            ]
        ]);
    }

    public function manageAction($uid, Request $request) {
        $user = User::get($uid);
        if( !$user instanceOf User ) throw new ModelNotFoundException("User [$uid] does not exists");
        return $this->viewResponse('admin/users/manage', [
            'user' => $user,
            'filter' => [
                '_action' => '/users',
                'q' => Text::get('admin-users-global-search')
            ]

        ]);
    }

    public function addAction(Request $request) {
        $processor = $this->getModelForm('AdminUserCreate', new User(), [], [], $request);
        $processor->createForm();
        $processor->getBuilder()
            ->add('submit', 'submit', [
                'label' => $submit_label ? $submit_label : 'regular-submit'
            ]);
        $form = $processor->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $request->isMethod('post')) {
            try {
                $processor->save($form, true);
                Message::info(Text::get('user-register-success'));
                return $this->redirect('/admin/users?' . $request->getQueryString());
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('admin/users/add', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    public function rolesAction($uid = null, Request $request) {
        $user = User::get($uid);
        $admin = Session::getUser();
        if( $user instanceOf User ) {
            $roles = $user->getRoles();
            $role_names = $roles->getRoleNames();
        } else {
            $roles = $admin->getRoles();
            $role_names = $roles::getAllRoleNames();
        }
        return $this->viewResponse('admin/users/role_list', [
            'user' => $user,
            'roles' => $roles,
            'role_names' => $role_names
        ]);
    }

    public function impersonateAction($uid, Request $request) {
        $admin = Session::getUser();
        $user = User::get($uid);
        if( !$user instanceOf User ) throw new ModelNotFoundException("User [$uid] does not exists");
        if(!$admin->canImpersonate($user)) {
            throw new ControllerAccessDeniedException("You don't have enough privileges to impersonate this user");
        }

        $referer = $request->headers->get('referer');
        $back = $referer ? $referer : '/admin/users';

        if(Session::exists('shadowed_by')) {
            Message::error(Text::get('admin-already-impersonating'));
            return $this->redirect($back);
        }

        Session::onSessionDestroyed(function () use ($user, $admin) {
            Message::error('User <strong>' . $admin->name . ' ('. $admin->id. ')</strong> converted to <strong>' . $user->name . ' ('. $user->id. ')</strong>');
        });
        Session::destroy();
        Session::setUser($user);
        Session::store('shadowed_by', [$admin->id, $admin->name, $back]);

        // Feed Event
        // TODO: throw event instead
        $log = new Feed();
        $log->setTarget($admin->id, 'user')
            ->populate(
                Text::sys('feed-admin-impersonated'),
                '/admin/users/manage/' . $user->id,
            new FeedBody(null, null, 'feed-admin-has-done', [
                    '%ADMIN%' => Feed::item('user', $admin->name, $admin->id),
                    '%USER%'    => Feed::item('user', $user->name, $user->id),
                    '%ACTION%'  => new FeedBody('relevant', null, 'feed-admin-impersonated-action')
                ])
            )
            ->doAdmin('user');

        if(!$referer || strpos($referer, '/admin')) $referer = '/dashboard';
        return $this->redirect($referer);
    }

}
