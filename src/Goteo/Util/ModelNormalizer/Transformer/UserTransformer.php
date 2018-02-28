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

// $item['id'] = $ob->id;
// $item['avatar'] = $ob->avatar->name ? $ob->avatar->getLink(64, 64) : '';
// $item['name'] = $ob->name;
// $item['email'] = $ob->email;
// $item['roles'] = $ob->getRoles()->getRoleNames();
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
        return ['id', 'fullname', 'email', 'roles'];
    }

    function getAvatar() {
        return $this->model->avatar->name ? $this->model->avatar->getLink(64, 64) : '';
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

    function getRoles() {
        return $this->model->getRoles()->getRoleNames();
    }
}
