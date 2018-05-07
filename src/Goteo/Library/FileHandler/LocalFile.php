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

class LocalFile extends BaseFile implements FileInterface {

    static public $base_dir;

	public function __construct($path='') {
        static::$base_dir = \GOTEO_DATA_PATH;
        $this->setPath($path);
        parent::__construct($path);
	}


    /**
     * Returns false if cannot connect o change to specified path
     * */
    public function connect() {
        $connected = false;

        if($this->link) {
            $connected = true;
        } elseif($this->realpath()) {
            $this->link = true;
            $connected = true;
        } else {
            $this->throwError('file-chdir-error');
        }

        return $connected;
    }

    /**
     * Close the current connection
     * @return [type] [description]
     */
    public function close() {
        $this->link = null;
        return true;
    }

    /**
     * Tests a absolute path on the active connection
     * @param  string $path the path for to check existence
     * @return string       returns the real path directory or false on failure
     */
    public function realpath($path='') {
        $realpath = false;
        if (empty($path)) {
            $remote = $this->path;
        } else {
            $remote = $this->get_path($path);
        }

        $realpath = realpath($remote);

        // TODO: formatPath
        if($realpath && substr($realpath, 1, -1) != DIRECTORY_SEPARATOR) {
            $realpath .= DIRECTORY_SEPARATOR;
        }

        return $realpath;
    }

    /**
     * stores file on remote (overwrites)
     * @param  string $local  local file (must be absolute or relative to the working document)
     * @param  string $remote remote file (relative to $this->path)
     * @param  boolean $extra if $extra['auto_create_dirs'] true tries to autocreates the directory structure on remote
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

        if(is_file($local)) {
            if(!isset($extra['auto_create_dirs']) || $extra['auto_create_dirs'] === true) {
                $this->mkdir_recursive(dirname($remote));
            }

            if(copy($local, $remote)) {
                $ok = true;
            } else {
                return $this->throwError("file-error-uploading-to: [$local => $remote] (folder perms perhaps?)");
            }
        }

        return $ok;
    }

    /**
     * deletes file on remote
     * @param  string  $remote remote file (relative to $this->path) that will be deleted
     * @param  mixed $extra if $extra['auto_delete_dirs'] true deletes empty the directory (and all empty parents) containing the remote file
     *                      if string, will be used as a prefix to not delete directories that matches
     * @return boolean      returns true if success, false otherwise
     */
    public function delete($remote, $extra = array()) {
        if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);
        $remote = $this->get_path($remote);

        $ok = false;

        $auto_delete_dirs = $extra['auto_delete_dirs'];

        if(@unlink($remote)) {
            $ok = true;
            if($auto_delete_dirs) {
    			$this->delete_empty_dir(dirname($remote), is_string($auto_delete_dirs) ? $auto_delete_dirs : false);
            }
        } else {
            return $this->throwError("file-error-deleting-to: " . $this->last_error);
        }

        return $ok;
    }


    /**
     * From: http://php.net/manual/es/function.rmdir.php#110489
     */
    private function delTree($dir) {
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

        if(self::delTree($remote)) {
            $ok = true;
        } else {
	return $this->throwError("file-error-rmdir-to: " . $this->last_error);
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

        if(is_dir($remote_dir) && count(scandir($remote_dir)) == 2) {
            if(@rmdir($remote_dir)) return $this->delete_empty_dir(dirname($remote_dir), $top_max_dir);
        }

        return true;
    }

    /**
     * Creates a dir in remote recursively
     * @param  string $remote_dir absolute remote dir!
     * @return [type]             [description]
     */
    protected function mkdir_recursive($remote_dir) {
        $ok = true;

        if(!is_dir($remote_dir)) {
            $ok = @mkdir($remote_dir, 0777, true);
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

        if(copy($remote, $local)) {
            $ok = true;
        } else {
            return $this->throwError("file-error-downloading-from: " . $this->last_error);
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

        if($auto_create_dirs) $this->mkdir_recursive(dirname($remote_dest));
        if(rename($remote_source, $remote_dest)) {
            $ok = true;
            if($auto_delete_dirs) $this->delete_empty_dir(dirname($remote_source));
        } else {
	return $this->throwError("file-error-renaming-to: " . $this->last_error);
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

        if( !($size = filesize($remote)) ) $size = -1;

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

        if( false === ($modified = @filemtime($remote)) ) $modified = -1;

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
        return file_exists($filepath);
    }

    /**
     * Like file_get_contents()
     * @param  string $remote remote file
     * @return string         file raw data
     */
    public function get_contents($remote_original) {
        if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);
        $remote = $this->get_path($remote_original);

        return file_get_contents($remote);
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

        return file_put_contents($remote, $data, $flags);
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
        if($dir === '.') $dir = '';
        if($dir) $dir = "$dir/";
        $name = basename($remote_original);
        $name = preg_replace("/[^a-z0-9_~\.-]+/","-",strtolower(Model::idealiza($name, true)));
        $remote = $this->get_path($dir . $name);

        while ( file_exists ( $remote )) {
            $name = preg_replace_callback( '/^(.+?)(\-?)(\d*)(\.[^.]+)?$/', function($m){
                return $m[1] .'-' . ((int)$m[3]+1) . $m[4];
            }, $name );
            $remote = $this->get_path($dir . $name);
        }
        return $name;
    }

    /**
     * Reimplementation to remove leading slash
     */
    public function setPath($path) {
        while(substr($path,0,1) == DIRECTORY_SEPARATOR) $path = substr($path, 1);

        $this->path = static::$base_dir . $path;
        if(!is_dir($this->path)) @mkdir($this->path, 0777, true);
    }

}
