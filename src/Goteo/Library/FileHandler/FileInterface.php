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

    interface FileInterface {
        public function connect();
        public function close();
        public function realpath($path='');

        public function upload($local, $remote, $extra = array());
        public function delete($remote, $extra = array());

        public function rmdir($remote_dir_original);
        public function download($remote, $local);
        public function rename($remote_source, $remote_dest, $auto_create_dirs = true, $auto_delete_dirs = true);
        public function size($remote_original, $force=false);
        public function mtime($remote_original, $force=false);
        public function exists($filename);
        public function get_contents($remote_original);
        public function put_contents($remote_original, $data, $flags = 0, $perms = 'public-read', $metaHeaders = array(), $requestHeaders = array());
        public function get_save_name($remote_original);

    }

}
