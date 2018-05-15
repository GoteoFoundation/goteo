<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
namespace Goteo\Util\ModelNormalizer\Transformer;

use Goteo\Model\User;

/**
 * Transform a Model\User

// $item['amount'] = $ob->amount;
// // $item['num_owned'] = $ob->num_owned;
// // $item['num_invested'] = $ob->num_invested;
// $projects = $ob->getProjectNames();
// if($projects) {
//     $item['roles'][] = 'owner';
// }
// $item['info'] = $projects;


 */
class UserTransformer extends AbstractTransformer {
    public function getDefaultKeys() {
        return ['id', 'fullname', 'amount', 'roles'];
    }

    function getAvatar() {
        return $this->model->avatar->name ? $this->model->avatar->getLink(64, 64, true) : '';
    }

    /**
     * Like getName but Fullname view includes avatar image
     */
    function getFullname() {
        return $this->getName();
    }

    function getEmail() {
        return $this->model->email;
    }

    function getAmount() {
        return (int) $this->model->amount;
    }

    function getProjects() {
        return $this->model->getProjectNames();
    }

    function getRoles() {
        $roles = $this->model->getRoles();
        $names = $roles->getRoleNames();
        if($projects = $this->getProjects()) {
            $names['owner'] = $roles::getRoleName('owner');
        }
        return $names;
    }

    function getLink($type = 'public') {
        if($type === 'admin') {
            return '/admin/users/manage/' . $this->getId();
        }
        return '/user/profile/' . $this->getId();
    }

    function getInfo() {
        return $this->getRoles();
    }
}
