<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Core\Traits;

use Goteo\Core\Model;
use Monolog\Logger;
use Goteo\Util\Monolog\Processor\WebProcessor;

/**
 */
trait LockTrait {

    // checks if it's already locked
    public function isNamedLocked($lockname) {
        $rs = Model::query("SELECT IS_FREE_LOCK(:lockname)", [':lockname' => $lockname]);
        if($rs->fetchColumn()) {
            // it it's free, let's double check in a while
            usleep(10000); // wait 10 milliseconds and try again to minimize collisions likelihood
            $rs = Model::query("SELECT IS_FREE_LOCK(:lockname)", [':lockname' => $lockname]);
            return !$rs->fetchColumn();
        }
        return true;
    }

    // Define the two functions for locking
    public function getNamedLock($lockname) {
        if(!$this->isNamedLocked($lockname)) {
            $rs = Model::query("SELECT GET_LOCK(:lockname, 0)", [':lockname' => $lockname]);
            return $rs->fetchColumn();
        }
        return false;
    }

    public function releaseNamedLock($lockname) {
        Model::query("DO RELEASE_LOCK(:lockname)", [':lockname' => $lockname]);
        return !$this->isNamedLocked($lockname);
    }

}
