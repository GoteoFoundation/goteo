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

use FileSystemCache;
use FileSystemCacheKey;

/**
 * General cache class
 * For the moment, just a wrapper to the FileSystemCache
 *
 * View doc: https://github.com/jdorn/FileSystemCache
 */
class Cacher {

    /**
     * Time valid time, 0 means infinite
     * @var integer
     */
    private $cacheTime = 0;

    /**
     * Caches will be grouped, default group is misc
     * @var string
     */
    private $group = 'misc';

    /**
     * Constructor, set the group to isolate the caches in the instance
     * @param string  $group cache group
     * @param integer $time  ttl time by default (infinite)
     */
    public function __construct($group = '', $time = 0) {
        if($time > 0) $this->cacheTime = (int) $time;
        if($group) $this->group = $group;
    }

    /**
     * Sets the default cache group
     * @return [type] [description]
     */
    public function setCacheGroup($group) {
        return $this->group = $group;
    }

    /**
     * Returns the cache group
     * @return [type] [description]
     */
    public function getCacheGroup() {
        return $this->group;
    }

    /**
     * Sets the default cache time
     * @return [type] [description]
     */
    public function setCacheTime($cacheTime) {
        return $this->cacheTime = $cacheTime;
    }

    /**
     * Returns the cache time
     * @return [type] [description]
     */
    public function getCacheTime() {
        return $this->cacheTime;
    }

     /**
     * Generates a cache key to use with store, retrieve, getAndModify, and invalidate
     * @param mixed $key_data Unique data that identifies the key.  Can be a string, array, number, or object.
     * @param String $group An optional group to put the cache key in.  Must be in the format "groupname" or "groupname/subgroupname".
     * @return FileSystemCacheKey The cache key object.
     */
    public function getKey($key_data, $group = null) {
        $group = $this->group . '/' . $group;
        return FileSystemCache::generateCacheKey($key_data, $group);
    }

    /**
     * Stores data in the cache
     * @param FileSystemCacheKey $key The cache key
     * @param mixed $data The data to store (will be serialized before storing)
     * @param int $ttl The number of seconds until the cache expires.  (optional)
     * @return boolean True on success, false on failure
     */
    public function store(FileSystemCacheKey $key, $data, $ttl=null) {
        return FileSystemCache::store($key, $data, $ttl);
    }

    public function modify(FileSystemCacheKey $key, Closure $callback, $resetTtl=false) {
        return FileSystemCache::getAndModify($key, $callback, $resetTtl=false);
    }

    public function invalidate(FileSystemCacheKey $key) {
        return FileSystemCache::invalidate($key);
    }

    /**
     * Retrieve data from cache
     * @param FileSystemCacheKey $key The cache key
     * @param int $newer_than If passed, only return if the cached value was created after this time
     * @return mixed The cached data or FALSE if not found or expired
     */
    public function retrieve(FileSystemCacheKey $key, $newer_than=null) {
        return FileSystemCache::retrieve($key, $newer_than);
    }

    /**
     * Returns a full path of the cache of the requested file
     * auto-creates parent dirs if necessary
     *
     * @param  string $file  file
     * @param  string $group group
     * @return string        file
     */
    public function getFile($file, $group = '') {
        $pathinfo = pathinfo($file);
        $dirname = preg_replace('/[^a-zA-Z0-9_\-]/','',$pathinfo['dirname']);

        $key = $this->getKey($pathinfo['basename'], $dirname . $group);

        $file = self::getCacheDir() . $key->__toString();
        $dirname = dirname($file);
        if(!is_dir($dirname)) {
            mkdir($dirname, 0777, true);
        }

        return $file;
    }

    /**
     * General purpose method to stream directly to the browser the file
     * @return boolean true if file exists, false otherwise
     */
    static function flushFile($file) {
        if(is_file($file)) {
            $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file);

            header('Content-Type: ' . $mime);
            header('Content-Length: ' . filesize($file));
            readfile($file);

            return true;
        }
        return false;
    }

    /**
     * Returns the full path of the provide key
     * If dirs need to be created, so will do it
     * @param FileSystemCacheKey $key
     */
    public function getFileCached($key) {
        $file = self::getCacheDir() . $key->__toString();
        $dirname = dirname($file);
        if(!is_dir($dirname)) {
            mkdir($dirname, 0777, true);
        }
        return $file;
    }

    /**
     * Gets the cache dir (static value) whit / char at the end
     */
    static function getCacheDir() {
        $dir = FileSystemCache::$cacheDir;
        if(substr($dir, -1, 1) != DIRECTORY_SEPARATOR) $dir .= DIRECTORY_SEPARATOR;
        return $dir;
    }

    /**
     * Set up the cache dir (static value)
     */
    static function setCacheDir($dir) {
        if(is_dir($dir) && is_writable($dir)) {
            return FileSystemCache::$cacheDir = $dir;
        }
        else {
            //throw exception maybe?
            return false;
        }
    }


    /**
     * Invalidates full cache
     * @param  string $group The group to invalidate ex: group/subgroup1
     * @return [type]       [description]
     */
    public function clean() {
        return FileSystemCache::invalidateGroup($this->group);
    }
}
