<?php

namespace Goteo\Library {

    // require_once "library/aws/aws.phar"; //AWS SDK PHAR
    require_once "library/aws/S3.php"; //AWS SDK normal

    use Goteo\Core\Model,
        Goteo\Core\Error,
        Goteo\Core\Exception;

    use \S3;

    /*
     * Capa de abstracción para el manejo de archivos en funcion de la configuracion
     *
     */

    /**
    * @file classes/file.php
    * @author Ivan Vergés
    * @brief FILE wrapper manipulation class\n
    * This file is used to upload, download on several services like, local, AmazonS3\n
    * This class is used by the file functions/file.php
    *
    * @section usage Usage
    * $db = new File('type','host','port','username','password','path');\n
    * $db->connect();\n
    * $db->upload($local_file, $remote_file);\n
    *
    * Uso en GOTEO
    * ============
    *
    * //archivos de prueba (sin / delante)
    * $original = "images/la_gota.png";
    * $cache = "cache/directorio/la_gota.png";
    * //nombre de archivo temporal para pruebas
    * $tmp = tempnam(sys_get_temp_dir(), 'goteo-temp');
    *
    * //creacion del objecto s3 o local en funcion de la configuracion de goteo
    * $fp = new File();
    *
    *
    * //obtencion de tamaño (bytes) de un archivo
    * echo $fp->size($original) . " bytes";
    *
    * //obtencion fecha de modificacion archivo (unixtime)
    * echo $fp->mtime($original) . " modified";
    *
    * //descarga de un archivo (copia) a local
    * $fp->download($original, $tmp);
    * echo filesize($tmp)." b downloaded";
    *
    * //subida de un archivo local a s3 (copia si local)
    * $fp->upload($tmp, $cache);
    * //para comprovar si la url existe
    * if(Goteo\Library\Cache::url_exists(SRC_URL . "/$cache")) echo SRC_URL . "/$cache Exists!";
    *
    * //renombrar un archivo remoto
    * $fp->rename($cache, $cache.".renamed");
    *
    * //borrar un archivo remoto
    * $fp->delete($cache.".renamed");
    * //comprueba si se ha borrado
    * if(!Goteo\Library\Cache::url_exists(SRC_URL . "/$cache.renamed")) echo SRC_URL . "/$cache.renamed Deleted!<br>";
    *
    */
    class File {
        public $type = '';
        private $link = '', $user = '', $pass = '', $path = '', $bucket = '';
        public  $last_error = '', $last_path = '', $last_local = '', $last_remote = '';
        private $quiet_mode = false;

        /**
         * Sets the initial parameters to work
         * Depending on the configuration it will be an instance to manage local files or files at Amazon S3
         */
        public function __construct($bucket = AWS_S3_BUCKET_STATIC, $error_mode = 'error') {
            if(defined("FILE_HANDLER") && FILE_HANDLER == 's3') {
                $this->type = 's3';
                $this->user = AWS_KEY;
                $this->pass = AWS_SECRET;
                $this->bucket = $bucket;
            } else {
                $this->type = 'file';
                $this->path = dirname(__DIR__) . '/data';
                if (substr($this->path, -1, 1) != DIRECTORY_SEPARATOR) $this->path .= DIRECTORY_SEPARATOR;
            }

            $this->error_mode($error_mode);
        }

        /**
         * Sets (new) bucket and prefix. Instance type must be S3 already.
         * @param $bucket
         * @param $prefix
         *
         */
        public function setBucket($bucket, $prefix='') {
            if ($this->type != 's3') {
                throw new \RuntimeException("Cannot set bucket if the instance type is not Amazon S3");
            }

            $this->bucket = $bucket;
            $this->path = $prefix;
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
        public function error_mode($mode = 'exception') {
            if($mode == 'exception')  $this->quiet_mode = false;
            elseif($mode == 'quiet')  $this->quiet_mode = 1;
            elseif($mode == 'string') $this->quiet_mode = 2;
            elseif($mode == 'die')    $this->quiet_mode = 3;
            elseif($mode == 'error')  $this->quiet_mode = 4;
        }

        /**
         *
         */
        public static function s3_acl($perm = 'public-read') {
            if(!is_string($perm) || !in_array($perm, array('public-read', 'public-read-write', 'authenticated-read', 'bucket-owner-read', 'bucket-owner-full-control')))
                $perm = 'public-read';
            return $perm;
        }

        /**
         * Returns false if cannot connect o change to specified path
         * */
        public function connect() {
            $connected = false;

            switch($this->type) {
                case 'file':
                    if($this->link) {
                        $connected = true;
                    } elseif($this->realpath($this->path)) {
                        $this->link = true;
                        $connected = true;
                    } else {
                        $this->throwError('file-chdir-error');
                    }
                    break;

                case 's3':
                    if($this->link instanceOf \S3) {
                        $connected = true;
                    } else {

                        $this->link = new \S3($this->user, $this->pass);

                        if ($this->link->getBucketLocation($this->bucket) !== false) {
                            $connected = true;
                        } else {
                            $this->link = false;
                            $this->throwError($e->getMessage());
                        }
                    }
                    break;

            }

            return $connected;
        }

        /**
         * Close the current connection
         * @return [type] [description]
         */
        public function close() {
            $ok = true;
            switch($this->type) {
                case 'file':
                    // TODO
                    break;

                case 's3':
                    if( !($this->link instanceOf \S3) ) {
                        $ok = false;
                    }
                    break;
            }

            $this->link = null;
            return $ok;
        }

        /**
         * Ensures a valid path without duplicates /
         * @param  [type] $path [description]
         * @return [type]         [description]
         */
        public static function path($path) {
            while($path{0} == DIRECTORY_SEPARATOR) $path = substr($path, 1);
            if(substr($path, -1, 1) != DIRECTORY_SEPARATOR) $path .= DIRECTORY_SEPARATOR;
            return $path;
        }

        /**
         * Ensures a valid absolute path without duplicates /
         * @param  [type] $remote [description]
         * @return [type]         [description]
         */
        public function get_path($remote='') {
            while($remote{0} == '/') $remote = substr($remote,1);

            if (!empty($path)) {
                $path = $this->path;
                while(substr($path, -1) == DIRECTORY_SEPARATOR) $path = substr($path, 0, -1);
                $remote = $path . DIRECTORY_SEPARATOR . $remote;
            }

            return $remote;
        }

        /**
         * Tests a absolute path on the active connection
         * @param  string $path the path for to check existence
         * @return string       returns the real path directory or false on failure
         */
        public function realpath($path='') {
            $this->last_path = $path;
            $realpath = false;

            if($this->link && $path) {

                switch($this->type) {
                    case 'file':
                        $realpath = realpath($path);
                        if (!$realpath) {
                            return $this->throwError("{$path} not found: " . $this->last_error);
                        }
                        break;

                    case 's3':
                        $realpath = $this->get_path($path);
                        break;
                }
            }

            if($realpath && substr($realpath, 1, -1) != DIRECTORY_SEPARATOR) {
                $realpath .= DIRECTORY_SEPARATOR;
            }

            return $realpath;
        }

        /**
         * stores file on remote (overwrites)
         * @param  string $local  local file (must be absolute or relative to the working document)
         * @param  string $remote remote file (relative to $this->path)
         * @param  boolean $auto_create_dirs if true tries to autocreates the directory structure on remote
         *                                   on S3 has no effect as AWS has no concept of "directory" (always true)
         *                                   so its used as a Permission control, string expecte of one of this values:
         *                                   public-read, public-read-write, authenticated-read, bucket-owner-read, bucket-owner-full-control (default public-read)
         * @return boolean        returns true if success, false otherwise
         */
        public function upload($local, $remote, $auto_create_dirs = true) {
            if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);
            $remote = $this->get_path($remote);
            $this->last_local  = $local;

            $ok = false;

            //if local is a stream, copy locally
            if(substr($local,0,7) == 'http://') {
                $tmp = array_search('uri', @array_flip(stream_get_meta_data($GLOBALS[mt_rand()]=tmpfile())));
                file_put_contents($tmp, file_get_contents($local));
                $local = $tmp;
            }

            if(!is_file($local)) {
                return $this->throwError("local-file-not-exists: $local");
            }
            switch($this->type) {
                case 'file':
                    if($auto_create_dirs) {
                        $this->mkdir_recursive(dirname($remote));
                    }

                    if(copy($local, $remote)) {
                        $ok = true;
                    } else {
                        return $this->throwError("file-error-uploading-to: " . $this->last_error);
                    }
                    break;

                case 's3':
                    // TODO para todos
                    //if ($this->link->putObject(S3::inputFile($local), $this->bucket, 'mail/' . $remote, ACL_PUBLIC_READ)) {
                    if ($this->link->putObject(S3::inputFile($local), $this->bucket, $remote, self::s3_acl($auto_create_dirs))) {
                        $ok = true;
                    }

                    break;
            }

            return $ok;
        }

        /**
         * deletes file on remote
         * @param  string  $remote remote file (relative to $this->path) that will be deleted
         * @param  mixed $auto_delete_dirs if true deletes empty the directory (and all empty parents) containing the remote file
         *                                 if string, will be used as a prefix to not delete directories that matches
         *                                 on AWS S3, is always true (there's no directories)
         * @return boolean        returns true if success, false otherwise
         */
        public function delete($remote, $auto_delete_dirs = true) {
            if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);
            $remote = $this->get_path($remote);

            $ok = false;

            switch($this->type) {
                case 'file':
                    if(unlink($remote)) {
                        $ok = true;
                        if($auto_delete_dirs) $this->delete_empty_dir(dirname($remote), is_string($auto_delete_dirs) ? $auto_delete_dirs : false);
                    }
                    else return $this->throwError("file-error-deleting-to: " . $this->last_error);
                    break;

                case 's3':
                    if ($this->link->deleteObject($this->bucket, $remote)) {
                        $ok = true;
                    } else {
                        return $this->throwError("Failed to delete file");
                    }
                    break;
            }

            return $ok;
        }

        /**
         * From: http://php.net/manual/es/function.rmdir.php#110489
         */
        public static function delTree($dir) {
            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        }

        /**
         * Deletes a remote directory recursively
         * @param  string $remote_dir the remote dir
         * @return boolean        returns true if success, false otherwise
         */
        public function rmdir($remote_dir_original) {
            if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);
            if(!$remote_dir_original) return $this->throwError("remote-dir-error [$remote_dir_original]");
            $remote = $this->get_path($remote_dir_original);
            //never delete the working path
            if($remote == $this->get_path()) return $this->throwError("remote error: [$remote] != [" . $this->get_path() . "]");

            $ok = false;

            switch($this->type) {
                case 'file':
                        if(self::delTree($remote)) {
                            $ok = true;
                        }
                        else return $this->throwError("file-error-rmdir-to: " . $this->last_error);
                    break;

                case 's3':

                    if (($contents = $this->link->getBucket($this->bucket, $remote)) !== false) {
                        foreach ($contents as $object) {
                            // print_r($object);
                            $this->link->deleteObject($this->bucket, $object->name);
                        }
                    } else {
                        return $this->throwError("s3-error-deleting-to: " . $e->getMessage());
                    }

                    break;

            }

            return $ok;
        }

        /**
         * Deletes a directory if its empty on remote place
         * @param  string $remote absolute remote dir!
         * @param  mixed  $top_max_dir, if is string, directories matching will not be deleted (relative to root path)
         * @return [type]         [description]
         */
        protected function delete_empty_dir($remote_dir, $top_max_dir = false) {
            if(!$this->connect()) return false;
            //never delete the root path
            if($remote_dir == $this->path) return true;
            if(is_string($top_max_dir) && $this->get_path($top_max_dir) == $remote_dir) return true;

            switch ($this->type) {
                case 'file':
                    if(is_dir($remote_dir) && count(scandir($remote_dir)) == 2) {
                        if(@rmdir($remote_dir)) return $this->delete_empty_dir(dirname($remote_dir), $top_max_dir);
                    }
                    break;
                case 's3':
                    // TODO
                    break;

            }
            return true;
        }

        /**
         * Creates a dir in remote recursively
         * @param  string $remote_dir absolute remote dir!
         * @return [type]             [description]
         */
        protected function mkdir_recursive($remote_dir) {
            if(!$this->connect()) return false;
            $ok = true;
            switch($this->type) {
                case 'file':
                    if(!is_dir($remote_dir)) {
                        $ok = @mkdir($remote_dir, 0777, true);
                    }
                    break;
                case 's3':
                    // TODO
                    break;

            }

            return $ok;
        }

        /**
         * retrieves file from remote (overwrites)
         * @param  string $remote remote file (relative to $this->path)
         * @param  string $local  local file (must be absolute or relative to the working document)
         * @return boolean        returns true if success, false otherwise
         */
        public function download($remote, $local) {
            if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);
            $remote = $this->get_path($remote);
            $this->last_local = $local;

            $ok = false;

            if(substr($local,0,7) == 'http://') {
                $tmp = array_search('uri', @array_flip(stream_get_meta_data($GLOBALS[mt_rand()]=tmpfile())));
                file_put_contents($tmp, file_get_contents($local));
                $local = $tmp;
            }

            switch($this->type) {
                case 'file':
                    if(copy($remote, $local)) {
                        $ok = true;
                    } else {
                        return $this->throwError("file-error-downloading-from: " . $this->last_error);
                    }
                    break;

                case 's3':
                    if (($this->link->getObject($this->bucket, $remote, $local)) !== false) {
                        $ok = true;
                    }
                    break;
            }

            return $ok;
        }

        /**
         * Rename files on remote (overwrites)
         * @param  string $remote_source remote file origin
         * @param  string $remote_dest   remote file destination
         * @param  boolean $auto_create_dirs if true tries to autocreates the directory structure on remote
         *                                   on S3 has no effect as AWS has no concept of "directory" (always true)
         *                                   so its used as a Permission control, string expecte of one of this values:
         *                                   public-read, public-read-write, authenticated-read, bucket-owner-read, bucket-owner-full-control (default public-read)
         * @return boolean        returns true if success, false otherwise
         */
        public function rename($remote_source, $remote_dest, $auto_create_dirs = true, $auto_delete_dirs = true) {
            if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);
            $remote_source     = $this->get_path($remote_source);
            $remote_dest       = $this->get_path($remote_dest);
            if($remote_source == $remote_dest) return $this->throwError("files equals: [$remote_source] == [$remote_dest]");

            $ok = false;

            switch($this->type) {
                case 'file':
                    if($auto_create_dirs) $this->mkdir_recursive(dirname($remote_dest));
                    if(rename($remote_source, $remote_dest)) {
                        $ok = true;
                        if($auto_delete_dirs) $this->delete_empty_dir(dirname($remote_source));
                    }
                    else return $this->throwError("file-error-renaming-to: " . $this->last_error);
                    break;

                case 's3':
                    if (($this->link->copyObject($this->bucket, $remote_source, $this->bucket, $remote_dest, self::s3_acl($auto_create_dirs)))
                            && ($this->link->deleteObject($this->bucket, $remote_source))) {
                        $ok = true;
                    }

                    if (!$ok) {
                        return $this->throwError("Failed to rename file");
                    }

                    break;
            }

            return $ok;
        }

        /**
         * retrieves filesize from remote
         * @param  string  $remote remote file to check file size
         * @param  boolean $force  if it is true, then will try to download the file from ftp if ftp_size fails
         * @return int              returns -1 on error, file size otherwise
         */
        public function size($remote_original, $force=false) {
            if(!$this->connect()) return -1;
            $remote = $this->get_path($remote_original);
            $size = -1;

            switch($this->type) {
                case 'file':
                    if( !($size = filesize($remote)) ) $size = -1;
                    break;

                case 's3':
                    if (($info = $this->link->getObjectInfo($this->bucket, $remote)) !== false) {
                        $size = (int) $info->size;
                    } else {
                        $size = -1;
                        // return $this->throwError("Failed to retrieve filesize from remote");
                    }
                    break;
            }

            return $size;
        }

        /**
         * retrieves file modification time from remote
         * @param  string  $remote remote file to check file size
         * @param  boolean $force  if it is true, then will try to download the file from ftp if ftp_size fails
         * @return int              returns -1 on error, file size otherwise
         */
        public function mtime($remote_original, $force=false) {
            if(!$this->connect()) return -1;
            $remote = $this->get_path($remote_original);
            $modified = -1;

            switch($this->type) {
                case 'file':
                    if( false === ($modified = @filemtime($remote)) ) $modified = -1;
                    break;

                case 's3':
                    if (($info = $this->link->getObjectInfo($this->bucket, $remote)) !== false) {
                        $modified = strtotime($info->time);
                    } else {
                        $modified = -1;
                        // return $this->throwError("Failed to retrieve filesize from remote");
                    }
                    break;
            }

            return $modified;
        }

        /**
         * Like file_exists()
         * @param $filename
         * @return boolean
         */
        public function exists($filename) {
            if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);
            $filepath = $this->get_path($filename);
            $ok = false;

            switch($this->type) {
                case 'file':
                    $ok = file_exists($filepath);
                    break;

                case 's3':
                    $info = $this->link->getObjectInfo($this->bucket, $filepath);
                    $ok = ($info !== false);
                    break;
            }

            return $ok;
        }

        /**
         * Like file_get_contents()
         * @param  string $remote remote file
         * @return string         file raw data
         */
        public function get_contents($remote_original) {
            if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);
            $remote = $this->get_path($remote_original);
            $data = '';

            switch($this->type) {
                case 'file':
                    $data = file_get_contents($remote);
                    break;

                case 's3':
                    if (($object = $this->link->getObject($this->bucket, $remote)) !== false) {
                        $data = $object->body;
                    }

                    break;
            }

            return $data;
        }

        /**
         * Like file_put_contents (FILE_APPEND only as flag in S3, not very efficient)
         * @param $remote_original
         * @param $data
         * @param $flags
         * @param $perms (S3 only)
         * @param $metaHeaders (S3 only)
         * @param $requestHeaders (S3 only)
         * @param  boolean $perms on S3 its used as a Permission control, string expecte of one of this values:
         *                        public-read, public-read-write, authenticated-read, bucket-owner-read, bucket-owner-full-control (default public-read)
         *
         * @return [type] [description]
         */
        public function put_contents($remote_original, $data, $flags = 0, $perms = 'public-read', $metaHeaders = array(), $requestHeaders = array()) {
            if(is_array($data)) $data = implode("", $data);
            if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);
            $remote = $this->get_path($remote_original);

            $ok = false;
            switch($this->type) {
                case 'file':
                    $ok = file_put_contents($remote, $data, $flags);
                    break;

                case 's3':
                    $body = '';
                    if($flags == FILE_APPEND) {
                        if (($object = $this->link->getObject($this->bucket, $remote)) !== false) {
                            $body = $object->body;
                        }
                    }
                    $body .= $data;

                    if ($this->link->putObject($body, $this->bucket, $remote, self::s3_acl($perms), $metaHeaders, $requestHeaders)) {
                        $ok = true;
                    }
                    break;
            }
            
            return $ok;
        }

        /**
         * retrieves a non existing name from the remote place
         * @param  [type] $remote_original [description]
         * @return [type]                  [description]
         */
        public function get_save_name($remote_original) {
            if(!$this->connect()) return false;
            if(!$remote_original) return false;
            $dir = dirname($remote_original);
            if($dir) $dir = "$dir/";
            $name = basename($remote_original);
            $name = preg_replace("/[^a-z0-9_~\.-]+/","-",strtolower(Model::idealiza($name, true)));
            $remote = $this->get_path($dir . $name);

            switch($this->type) {
                case 'file':
                    while ( file_exists ( $remote )) {
                        $name = preg_replace ( "/^(.+?)(_|-?)(\d*)(\.[^.]+)?$/e", "'\$1-'.(\$3+1).'\$4'", $name );
                        $remote = $this->get_path($dir . $name);
                    }

                    break;

                case 's3':
                    //
                    $prefix = preg_replace ( "/^(.+?)(_|-?)(\d*)(\.[^.]+)?$/e", "'\$1'", $name );
                    $dir .= $prefix;

                    $info = $this->link->getObjectInfo($this->bucket, $dir . $name);
                    while($info !== false) {
                        $name = preg_replace ( "/^(.+?)(_|-?)(\d*)(\.[^.]+)?$/e", "'\$1-'.(\$3+1).'\$4'", $name );
                        $info = $this->link->getObjectInfo($this->bucket, $dir . $name);
                    }

                    break;
            }

            return $dir . $name;
        }

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
