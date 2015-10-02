<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library\FileHandler {
    abstract class BaseFile {

        protected $path, $link;

        public function __construct($path) {
            $this->path = $path;
            $this->link = false;
        }
        /**
         * Ensures a valid absolute path without duplicates /
         * @param  [type] $remote [description]
         * @return [type]         [description]
         */
         public function get_path($remote='') {
            while($remote{0} == '/') $remote = substr($remote,1);
            $path = $this->path;

            if (!empty($path)) {
                while(substr($path, -1) == DIRECTORY_SEPARATOR) $path = substr($path, 0, -1);
                $remote = $path . DIRECTORY_SEPARATOR . $remote;
            }

            return $remote;
        }


        /**
         * Ensures a valid path without duplicates leading / and with trailing /
         * @param  [type] $path [description]
         * @return [type]         [description]
         */
        protected function formatPath($path) {
            while($path{0} == DIRECTORY_SEPARATOR) $path = substr($path, 1);
            if(substr($path, -1, 1) != DIRECTORY_SEPARATOR) $path .= DIRECTORY_SEPARATOR;
            return $path;
        }

        /**
         *
         */
        public function setPath($path) {
            $this->path = $path;
        }

        /**
         *
         */
        protected function throwError($msg) {
            $this->last_error = "$msg";
            if($this->quiet_mode === false) throw new Exception($msg);
            elseif($this->quiet_mode === 2) return $msg;
            elseif($this->quiet_mode === 3) die($msg);
            elseif($this->quiet_mode === 4) throw new Error(500, $msg);
            return false;
        }

    }

}
