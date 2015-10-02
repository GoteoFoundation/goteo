<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Core {

    class Redirection extends Exception {

        const
            TEMPORARY   = 302,
            PERMANENT   = 301;

        private $url;

        public function __construct ($url, $code = self::TEMPORARY) {

            $this->url = $url;
            parent::__construct($url, $code);

        }

        public function getURL () {
            return $this->url;
        }

    }


}
