<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/


namespace Goteo\Library\FileHandler;

use Goteo\Application\Config;
use Goteo\Core\Model;
use S3;

class S3File extends BaseFile implements FileInterface {

    private $bucket, $user, $pass;

    public function __construct($user, $pass, $bucket='', $path='') {
            parent::__construct($path);
            $this->user = $user;
            $this->pass = $pass;
            $this->bucket = $bucket;
            $this->setPath($path);

            S3::setExceptions();
    }

    ////////////////////////////////////

    /**
     * Returns false if cannot connect o change to specified path
     * */
    public function connect() {
        $connected = false;

        if($this->link instanceOf S3) {
            $connected = true;
        } else {

            $this->link = new S3($this->user, $this->pass);

            if ($this->link->getBucketLocation($this->bucket) !== false) {
                $connected = true;
            } else {
                $this->link = false;
                $this->throwError("Failed to connect");
            }
        }

        return $connected;
    }

    /**
     * Close the current connection
     * @return [type] [description]
     */
    public function close() {
        $ok = true;

        if( !($this->link instanceOf S3) ) {
            $ok = false;
        }

        $this->link = null;
        return $ok;
    }


    /**
     * Tests a absolute path on the active connection
     * @param  string $path the path for to check existence
     * @return string       returns the real path directory or false on failure
     */
    public function realpath($path='') {
        $realpath = false;

        if($this->link) {
            // TODO: getObjectInfo returns false for directories
            $info = $this->link->getObjectInfo($this->bucket, $path);
            if ($info !== false) {
                $realpath = $path;
            }
        }

        return $realpath;
    }

    /**
     * stores file on remote (overwrites)
     * @param  string $local  local file (must be absolute or relative to the working document)
     * @param  string $remote remote file (relative to $this->path)
     * @param  boolean $extra $extra['perms'] is used as a Permission control, string expecte of one of this values:
     *                        public-read, public-read-write, authenticated-read, bucket-owner-read, bucket-owner-full-control (default public-read)
     * @return boolean        returns true if success, false otherwise
     */
    public function upload($local, $remote, $extra = array()) {
        if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);
        $remote = $this->get_path($remote);

        $ok = false;

        //if local is a stream, copy locally
        if(substr($local,0,2) == '//') $local = (Config::get('ssl') ? 'https:' : 'http:') . $local;
        if(substr($local,0,7) == 'http://' || substr($local, 0 , 8) == 'https://') {
            $tmp = array_search('uri', @array_flip(stream_get_meta_data($GLOBALS[mt_rand()]=tmpfile())));
            file_put_contents($tmp, file_get_contents($local));
            $local = $tmp;
        }

        $requestHeaders = array();

        //Tratar de descubrir el tipo mime
        if (extension_loaded('fileinfo')) {
            $finfo = finfo_open(FILEINFO_MIME);
            if(($type = finfo_file($finfo, $local)) !== false) {
                $requestHeaders = array('Content-Type' => $type);
            }
        }
        // die("[$local $remote]");
        if(is_file($local)) {

            if (!isset($extra['perms'])) {
		         $perms = 'public-read';
            } else {
		         $perms = $extra['perms'];
            }

            //if ($this->link->putObject(S3::inputFile($local), $this->bucket, $remote, ACL_PUBLIC_READ)) {
            if ($this->link->putObject(S3::inputFile($local), $this->bucket, $remote, $perms, array(), $requestHeaders)) {
                $ok = true;
            }
        }

        return $ok;
    }

    /**
     * deletes file on remote
     * @param  string  $remote remote file (relative to $this->path) that will be deleted
     * @param  mixed $extra not used
     * @return boolean        returns true if success, false otherwise
     *                        Note that Amazon doesn't give us any idea if the file existed before.
     *                        So true may be that it existed and was removed or that it didn't exist before
     */
    public function delete($remote, $extra = array()) {
        if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);
        $remote = $this->get_path($remote);

        $ok = false;

        // TODO
        /*
        if (!isset($extra['auto_delete_dirs'])) {
            $auto_delete_dirs = true;
        }
        */

        if ($this->link->deleteObject($this->bucket, $remote)) {
            $ok = true;
            // TODO
            /*
            if($auto_delete_dirs) {
                $this->delete_empty_dir(dirname($remote), is_string($auto_delete_dirs) ? $auto_delete_dirs : false);
            }
            */
        } else {
            return $this->throwError("Failed to delete file");
        }

        return $ok;
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

        if (($contents = $this->link->getBucket($this->bucket, $remote)) !== false) {
            foreach ($contents as $object) {
                // print_r($object);
                $this->link->deleteObject($this->bucket, $object->name);
            }
        } else {
            return $this->throwError("s3-error-deleting-to: " . $e->getMessage());
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

        $ok = false;

        if(substr($local,0,2) == '//') $local = (Config::get('ssl') ? 'https:' : 'http:') . $local;
        if(substr($local,0,7) == 'http://' || substr($local, 0 , 8) == 'https://') {
            $tmp = array_search('uri', @array_flip(stream_get_meta_data($GLOBALS[mt_rand()]=tmpfile())));
            file_put_contents($tmp, file_get_contents($local));
            $local = $tmp;
        }

        if (($this->link->getObject($this->bucket, $remote, $local)) !== false) {
            $ok = true;
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

        if (($this->link->copyObject($this->bucket, $remote_source, $this->bucket, $remote_dest, $this->s3_acl($auto_create_dirs)))
                && ($this->link->deleteObject($this->bucket, $remote_source))) {
            $ok = true;
        }

        if (!$ok) {
            return $this->throwError("Failed to rename file");
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

        $info = $this->link->getObjectInfo($this->bucket, $remote);
        if ($info !== false) {
            $size = (int) $info->size;
        } else {
            $size = -1;
            // return $this->throwError("Failed to retrieve filesize from remote");
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

        $info = $this->link->getObjectInfo($this->bucket, $remote);
        if ($info !== false) {
            $modified = $info['time'];
        } else {
            $modified = -1;
            // return $this->throwError("Failed to retrieve filesize from remote");
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

        $info = $this->link->getObjectInfo($this->bucket, $filepath);
        $ok = ($info !== false);

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

        if (($object = $this->link->getObject($this->bucket, $remote)) !== false) {
            $data = $object->body;
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

        $body = '';
        if($flags == FILE_APPEND) {
            if (($object = $this->link->getObject($this->bucket, $remote)) !== false) {
                $body = $object->body;
            }
        }
        $body .= $data;

        if ($this->link->putObject($body, $this->bucket, $remote, $this->s3_acl($perms), $metaHeaders, $requestHeaders)) {
            $ok = strlen($data);
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
        if(empty($remote_original)) return false;

        $dir = dirname($remote_original);
        if($dir === '.') $dir = '';
        if($dir) $dir = "$dir/";
        $name = basename($remote_original);
        $name = preg_replace("/[^a-z0-9_~\.-]+/","-",strtolower(Model::idealiza($name, true)));
        $remote = $this->get_path($dir . $name);

        $info = $this->link->getObjectInfo($this->bucket, $remote);

        while($info !== false) {
            $name = preg_replace_callback( '/^(.+?)(\-?)(\d*)(\.[^.]+)?$/', function($m){
                return $m[1] .'-' . ((int)$m[3]+1) . $m[4];
            }, $name );
            $remote = $this->get_path($dir . $name);
            $info = $this->link->getObjectInfo($this->bucket, $remote);
        }

        return $name;
    }

    /**
     * Reimplementation to remove leading slash
     */
    public function setPath($path) {
        while($path{0} == DIRECTORY_SEPARATOR) $path = substr($path, 1);

        $this->path = $path;
    }

    /**
     *
     */
    private function s3_acl($perm = 'public-read') {
        if(!is_string($perm) || !in_array($perm, array('public-read', 'public-read-write', 'authenticated-read', 'bucket-owner-read', 'bucket-owner-full-control')))
            $perm = 'public-read';
        return $perm;
    }
}

