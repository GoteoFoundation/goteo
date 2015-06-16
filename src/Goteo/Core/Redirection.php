<?php

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
