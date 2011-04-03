<?php

namespace Goteo\Core {

    class Redirection extends Error {

        const
            FOUND           = 302,
            MOVED           = 301,
            TEMPORARY       = 307,
            BAD_REQUEST     = 400,
            UNAUTHORIZED    = 401,
            PAYMENT_REQUEST = 402,
            FORBIDDEN       = 403,
            NOT_FOUND       = 404,
            NOT_ALLOWED     = 405,
            NOT_ACCEPTABLE  = 406,
            PROXY_REQUIRED  = 407,
            REQUEST_TIMEOUT = 408,
            CONFLICT        = 409,
            GONE            = 410;

        private $url;

        public function __construct ($url, $code = self::FOUND) {

            $this->url = $url;
            parent::__construct($code);

        }

        public function getURL () {
            return $this->url;
        }

    }


}