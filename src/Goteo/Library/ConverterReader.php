<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library;

/*
 * Clase para mantener una cache de rates
 */

class ConverterReader {
    private $url;
    private $result;

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function getUrl() {
        $this->url;
    }

    public function getResult() {
        $this->result;
    }

    /**
     *  Do a cUrl request
     *
     */
    public function get() {

        $curl = \curl_init();
        \curl_setopt( $curl, CURLOPT_URL, $this->url );
        \curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
        \curl_setopt( $curl, CURLOPT_USERAGENT, 'Goteo.org Currency Getter');

        $this->result = \curl_exec( $curl );

        \curl_close( $curl );


        return $this->result;
    }
}
