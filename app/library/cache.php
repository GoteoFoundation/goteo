<?php

namespace Goteo\Library {

    use Goteo\Core\Model;

    /**
     * Aquesta classe provee de métodos para la abstraccion de cache de archivos
     */

	class Cache {
		public $dir = '', $type = 'local', $url_prefix = '';
		protected $file = null;

		function __construct($dir = '', $type = 'local', $url_prefix = '') {
			if($type == 'local') {
				if(substr($dir, -1, 1) != DIRECTORY_SEPARATOR) $dir .= DIRECTORY_SEPARATOR;
				$this->type = 'local';
				$this->dir = dirname(dirname(__FILE__)) . '/data/' . $dir;
				if(!is_dir($this->dir)) {
					mkdir($this->dir, 0777, true);
				}
			}
			elseif($type instanceOf File) {
				$this->type = 'remote';
				$this->dir = $dir;
				$this->file = $type;
				$this->url_prefix = $url_prefix;
			}
		}


		function get_path($file='') {
			$dir = $this->dir;
			if(substr($dir, -1, 1) != DIRECTORY_SEPARATOR) $dir .= DIRECTORY_SEPARATOR;
			if($file{0} == '/') $file = substr($file, 1);
			return $dir . $file;
		}
		/**
		 * Returns a file (or url in remote) if cached version exists, false otherwise
		 * @param  string $file file to check
		 * @return mixed        string file or url
		 *                      boolean false if not exists
		 */
		function get_file($file) {
			$f = $this->get_path($file);
			if($this->type == 'local') {
				if(is_file($f)) {
					return $f;
				}
			}
			if($this->type == 'remote') {
				if($this->url_prefix) {
					if(self::url_exists($this->url_prefix . "/". $f)) {
						return $this->url_prefix . "/" . $f;
					}
				}
				else {
					if($this->file->size($f) != -1) {
						return $this->file->get_path($f);
					}
				}
			}
			return false;
		}

		/**
		 * Retuns true if the file is newer than the cache
		 * @param  [type] $file file to check
		 * @return [type]       [description]
		 */
		function expired($file, $checktime) {
			$f = $this->get_path($file);
			if($this->type == 'local') {
				if(!is_file($f) || filemtime($f) < $checktime) {
					return true;
				}
			}
			if($this->type == 'remote') {
				$time = $this->file->mtime($f);
				if($time == -1 || $time < $checktime) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Saves a file to a cache
		 * @param  [type] $local  [description]
		 * @param  [type] $remote [description]
		 * @return [type]         [description]
		 */
		function put_file($local, $remote) {
			$f = $this->get_path($remote);
			if($this->type == 'local') {
				if(is_file($local)) {
					$dir = dirname($f);
					if(!is_dir($dir)) {
						mkdir($dir, 0777, true);
					}
					$ok = copy($local, $f);
					@chmod($f, 0666);
					return $ok;
				}
			}

			if($this->type == 'remote') {
				return $this->file->upload($local, $f);
			}
			return false;
		}

		/**
		 * Deletes a cached file
		 * @param  [type] $remote cached file to delete
		 * @return [type]         [description]
		 */
		function del_file($remote) {
			$f = $this->get_path($remote);
			if($this->type == 'local') {
				return @unlink($f);
			}
			if($this->type == 'remote') {
				return $this->file->delete($f);
			}
		}

		/**
		 * @FIXME
		 * Dirty method to quickly delete local files by using system calls
		 * @return [type] [description]
		 */
		function rm($remote) {
			$f = $this->get_path($remote);
			if($this->type == 'local') {
				try {
					exec("rm -fr $f", $buffer, $result);
					if($result == 0) return true;
				}
				catch(Exception $e) {
				}
			}
			return false;
		}
		/**
		 * Comprueba si existe una URL
		 * @param  [type]  $url_original [description]
		 * @param  boolean $check_alive  [description]
		 * @return [type]                [description]
		 */
		static function url_exists($url_original, $check_alive = true) {
			$url = @parse_url($url_original);

		    if (!$url) {
		        return false;
		    }

		    if(!$check_alive) {
		        if(defined("FILTER_VALIDATE_URL")) {
		            return filter_var($url_original, FILTER_VALIDATE_URL);
		        }
		        else {
		            return preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url_original);
		        }
		    }

		    $url = array_map('trim', $url);
		    $url['port'] = (!isset($url['port'])) ? 80 : (int)$url['port'];
		    $path = (isset($url['path'])) ? $url['path'] : '';

		    if ($path == '') {
		        $path = '/';
		    }

		    $path .= (isset($url['query'])) ? "?$url[query]" : '';

		    if (isset($url['host']) AND $url['host'] != gethostbyname($url['host'])) {
		        if (PHP_VERSION >= 5)
		        {
		            $headers = @get_headers("$url[scheme]://$url[host]:$url[port]$path");
		        }
		        else
		        {
		            $fp = @fsockopen($url['host'], $url['port'], $errno, $errstr, 30);

		            if (!$fp)
		            {
		                return false;
		            }
		            fputs($fp, "HEAD $path HTTP/1.1\r\nHost: $url[host]\r\n\r\n");
		            $headers = fread($fp, 4096);
		            fclose($fp);
		        }
		        $headers = (is_array($headers)) ? implode("\n", $headers) : $headers;
		        return (bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
		    }
		    return false;
		}

		function throwError($msg='') {
			throw new Exception($msg);
		}
	}
}
