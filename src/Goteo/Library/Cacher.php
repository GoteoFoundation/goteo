<?php

namespace Goteo\Library;

use FileSystemCache;

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

    public function __construct($group = '', $time = 0) {
        if($time > 0) $this->cacheTime = (int) $time;
        if($group) $this->group = $group;
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
    public static function store(FileSystemCacheKey $key, $data, $ttl=null) {
        return FileSystemCache::store($key, $data, $ttl);
    }

    /**
     * Retrieve data from cache
     * @param FileSystemCacheKey $key The cache key
     * @param int $newer_than If passed, only return if the cached value was created after this time
     * @return mixed The cached data or FALSE if not found or expired
     */
    public static function retrieve(FileSystemCacheKey $key, $newer_than=null) {
        return FileSystemCache::retrieve($key, $newer_than);
    }

    /**
     * Set up the cache dir (static value)
     */
    static function setCacheDir($dir) {
        return FileSystemCache::$cacheDir = $dir;
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
