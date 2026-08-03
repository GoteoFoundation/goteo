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

class ConverterReader
{
    private $url;
    private $result;

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getResult()
    {
        return $this->result;
    }

    /**
     *  Do a cUrl request
     *
     */
    public function get()
    {

        $curl = curl_init($this->url);

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'Goteo.org',
        ]);

        $this->result = curl_exec($curl);

        if ($this->result === false) {
            error_log(curl_error($curl));
        }

        curl_close($curl);

        return $this->result;
    }
}
