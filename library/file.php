<?php

namespace Goteo\Library {

	// require_once "library/aws/aws.phar"; //AWS SDK PHAR
	require_once "library/aws/aws-autoloader.php"; //AWS SDK normal

    use Goteo\Core\Model,
    	Goteo\Core\Error,
    	Goteo\Core\Exception;

	use Aws\S3\S3Client;

    /*
     * Capa de abstracción para el manejo de archivos en funcion de la configuracion
     *
     */

    /**
	* @file classes/file.php
	* @author Ivan Vergés
	* @brief FILE wrapper manipulation class\n
	* This file is used to upload, download on several services like, local, ftp, AmazonS3\n
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
	* $fp = File::get();
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
		private $link = '', $host = '', $port = '', $user = '', $pass = '', $path = '', $bucket = '';
		public  $last_error = '', $last_path = '', $last_local = '', $last_remote = '';
		public  $ftp_pasv = true;
		private $quiet_mode = false;

		/**
		 * Sets the initial parameters to work
		 * @param string $type type of service, could be: file, ftp, s3
		 * @param string $host host to connect (ftp only)
		 * @param string $user username  to connect (ftp only), for s3 it's the access key
		 * @param string $pass password  to connect (ftp only), for s3 it's the secret key
		 * @param string $path base path to operate, for s3 it's the prefix (a / char will be added)
		 * @param string $port port to connect (ftp only), for s3 it's the bucket to operate
		 */
		function __construct($type = '', $host = '', $user = '', $pass = '', $path = '', $port = '') {
			$this->type = $type;
			$this->host = $host;
			$this->port = $port;
			$this->user = $user;
			$this->pass = $pass;
			$this->path = $path;
			if(substr($this->path, -1, 1) != DIRECTORY_SEPARATOR) $this->path .= DIRECTORY_SEPARATOR;
			if($this->type == 's3') {
				$this->bucket = $port;
				if(empty($path)) $this->path = '';
			}
		}

		/**
		 * Obtiene una instancia de FILE con la configuracion de goteo
		 * @return [type] [description]
		 */
		function get($error_mode = 'error') {
            if(defined("FILE_HANDLER") && FILE_HANDLER == 's3') {
                $fp = new self('s3','', AWS_KEY, AWS_SECRET, AWS_S3_PREFIX, AWS_S3_BUCKET);
            }
            else {
                $fp = new self('file', '', '', '', dirname(__DIR__) . '/data');
            }
			$fp->error_mode($error_mode);
            return $fp;
		}

		function error_mode($mode = 'exception') {
			if($mode == 'exception')  $this->quiet_mode = false;
			elseif($mode == 'quiet')  $this->quiet_mode = 1;
			elseif($mode == 'string') $this->quiet_mode = 2;
			elseif($mode == 'die') 	  $this->quiet_mode = 3;
			elseif($mode == 'error')  $this->quiet_mode = 4;
		}

		static function s3_acl($perm = 'public-read') {
			if(!is_string($perm) || !in_array($perm, array('public-read', 'public-read-write', 'authenticated-read', 'bucket-owner-read', 'bucket-owner-full-control')))
				$perm = 'public-read';
			return $perm;
		}

		/**
		 * Returns false if cannot connect o change to specified path
		 * */
		function connect() {
			set_error_handler(array($this,'error_handler'), E_ALL & ~E_NOTICE);
			switch($this->type) {
				case 'file':
						if($this->link) return true;

						$this->link = true;
						if($this->realpath($this->path)) {
							return true;
						}
						else {
							$this->link = false;
							$this->throwError('file-chdir-error');
						}
					break;

				case 'ftp':
						if(is_resource($this->link)) return true;

						if($this->link = @ftp_connect($this->host,(string)($this->port ? $this->port : 21) )) {
							if(@ftp_login($this->link, $this->user, $this->pass)) {
								// activate passive
								if($this->ftp_pasv) ftp_pasv($this->link, true);
								//test path
								if($this->realpath($this->path)) return true;
								else $this->throwError('ftp-chdir-error');
							}
							else $this->throwError('ftp-auth-error');

						}
						else $this->throwError('ftp-connection-error');
					break;


				case 's3':
						if($this->link instanceOf S3Client) return true;
						$this->link = S3Client::factory(array(
    						'key'    => $this->user,
    						'secret' => $this->pass,
    						'region' => AWS_REGION
						));
						try {
							//try to find the bucket by requesting his location
							$lc = $this->link->getBucketLocation(array('Bucket' => $this->bucket));
							return true;
						}catch(\Exception $e) {
							$this->link = null;
							$this->throwError($e->getMessage());
						}
					break;

			}

			restore_error_handler();
			return false;
		}

		/**
		 * Close the current connection
		 * @return [type] [description]
		 */
		function close() {
			$ok = true;
			switch($this->type) {
				case 'ftp':
						if(!is_resource($this->link)) return false;
						$ok = ftp_close($this->link);
					break;

				case 's3':
						if( !($this->link instanceOf S3Client) ) return false;
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
		static function path($path) {
			while($path{0} == '/') $path = substr($path, 1);
			if(substr($path, -1, 1) != '/') $path .= '/';
			return $path;
		}

		/**
		 * Ensures a valid absolute path without duplicates /
		 * @param  [type] $remote [description]
		 * @return [type]         [description]
		 */
		public function get_path($remote='') {
			while($remote{0} == '/') $remote = substr($remote,1);
			$path = $this->path;
			while(substr($path, -1) == '/') $path = substr($path, 0, -1);
			if($path) return "$path/$remote";
			else return $remote;
		}

		/**
		 * Tests a absolute path on the active connection
		 * @param  string $path the path for to check existence
		 * @return string       returns the real path directory or false on failure
		 */
		function realpath($path='') {
			$this->last_path = $path;
			$realpath = false;
			set_error_handler(array($this,'error_handler'), E_ALL & ~E_NOTICE);

			if($this->link && $path) {
				$realpath = '';
				switch($this->type) {
					case 'file':
							if( !($realpath = realpath($path)) ) {
								return $this->throwError("$path not found: " . $this->last_error);
							}
						break;

					case 'ftp':
							$p = @ftp_pwd($this->link);
							if(@ftp_chdir($this->link, $path)) {
								$realpath = ftp_pwd($this->link);
								@ftp_chdir($this->link, $p);
							}
							else {
								return $this->throwError("$path not found: " . $this->last_error);
							}
						break;

					case 's3':
							return $this->get_path($path);
						break;
				}
				if(substr($realpath, 1, -1) != DIRECTORY_SEPARATOR) $realpath .= DIRECTORY_SEPARATOR;
			}
			restore_error_handler();
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
		function upload($local, $remote, $auto_create_dirs = true) {
			if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);
			$remote = $this->get_path($remote);
			$this->last_local  = $local;
			$this->last_remote = $remote;

			$ok = false;
			set_error_handler(array($this,'error_handler'), E_ALL & ~E_NOTICE);

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
						if($auto_create_dirs) $this->mkdir_recursive(dirname($remote));
						if(copy($local, $remote)) $ok = true;
						else return $this->throwError("file-error-uploading-to: " . $this->last_error);
					break;

				case 'ftp':
						$dir = dirname($remote);
						$odir = '';
						if($auto_create_dirs) $this->mkdir_recursive($dir);
						if($dir != '.') {
							$odir = ftp_pwd($this->link);
							ftp_chdir($this->link, $dir);
						}
						if(ftp_put($this->link, basename($remote), $local, FTP_BINARY)) $ok = true;
						if($odir) ftp_chdir($this->link, $odir);
						if(!$ok) return $this->throwError("ftp-error-uploading-to: " . $this->last_error);
					break;

				case 's3':
						try {
							$this->link->putObject(array('Bucket' => $this->bucket, 'SourceFile' => $local, 'Key' => $remote, 'ACL' => self::s3_acl($auto_create_dirs)));
							// We can poll the object until it is accessible
							$this->link->waitUntilObjectExists(array('Bucket' => $this->bucket, 'Key' => $remote));
							$ok = true;
						}catch(\Exception $e) {
							return $this->throwError('s3-error-uploading-to: ' . $e->getMessage());
						}
					break;
			}
			restore_error_handler();
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
		function delete($remote, $auto_delete_dirs = true) {
			if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);

			$remote = $this->get_path($remote);
			$this->last_remote = $remote;

			$ok = false;
			set_error_handler(array($this,'error_handler'),E_ALL & ~E_NOTICE);
			switch($this->type) {
				case 'file':
						if(unlink($remote)) {
							$ok = true;
							if($auto_delete_dirs) $this->delete_empty_dir(dirname($remote), is_string($auto_delete_dirs) ? $auto_delete_dirs : false);
						}
						else return $this->throwError("file-error-deleting-to: " . $this->last_error);
					break;

				case 'ftp':
						$dir = dirname($remote);
						$odir = '';
						if($dir != '.') {
							$odir = ftp_pwd($this->link);
							ftp_chdir($this->link, $dir);
						}
						if(ftp_delete($this->link, basename($remote))) $ok = true;
						if($odir) ftp_chdir($this->link, $odir);
						if($auto_delete_dirs) $this->delete_empty_dir($dir, is_string($auto_delete_dirs) ? $auto_delete_dirs : false);
						if(!$ok) return $this->throwError("ftp-error-deleting-to: " . $this->last_error);
					break;

				case 's3':
						try{
							$this->link->deleteObject(array('Bucket' => $this->bucket, 'Key' => $remote));
							$ok = true;
						} catch(\Exception $e) {
							return $this->throwError("s3-error-deleting-to: " . $e->getMessage());
						}
					break;

			}
			restore_error_handler();
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
			$this->last_remote = $remote;

			$ok = false;
			set_error_handler(array($this,'error_handler'),E_ALL & ~E_NOTICE);
			switch($this->type) {
				case 'file':
						if(m_rmdir($remote)) {
							$ok = true;
						}
						else return $this->throwError("file-error-rmdir-to: " . $this->last_error);
					break;

				case 'ftp':
						//try to delete the dir or file
						$ok = false;
						 if( !(@ftp_rmdir($this->link, $remote) || @ftp_delete($this->link, $remote)) ) {
						 	//if the attempt to delete fails, get the file listing
							$filelist = @ftp_nlist($this->link, $remote);
							//loop through the file list and recursively delete the FILE in the list
							foreach($filelist as $file) {
								$file = preg_replace("/^" . str_replace("/", "\/", quotemeta($this->path)) . "/", "", $file);
								$this->rmdir($file);
							}
							//if the file list is empty, delete the DIRECTORY we passed
							$ok = $this->rmdir($remote_dir_original);
						}
						else $ok = true;
						if(!$ok) return $this->throwError("ftp-error-rmdir-to: " . $this->last_error);
					break;

				case 's3':
						try {
							$ok = false;
							$objectsIterator = $this->link->getIterator('ListObjects', array(
							    'Bucket' => $this->bucket,
							    'Prefix' => $remote
							), array(
							    'names_only' => true
							));
							foreach ($objectsIterator as $key) {
							    // echo $key . "\n";
							    $this->link->deleteObject(array('Bucket' => $this->bucket, 'Key' => $key));
							}
						} catch(\Exception $e) {
							return $this->throwError("s3-error-deleting-to: " . $e->getMessage());
						}
					break;

			}
			restore_error_handler();
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

				case 'ftp':
					if(@ftp_rmdir($this->link, $remote_dir)) return $this->delete_empty_dir(dirname($remote_dir), $top_max_dir);
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
			switch($this->type) {
				case 'file':
						if(!is_dir($remote_dir)) @mkdir($remote_dir, 0777, true);
						return is_dir($remote_dir);
					break;

				case 'ftp':
						$dir = $remote_dir;
						$odir = ftp_pwd($this->link);
						$parts = explode("/", $dir);
				        $ok = true;
				        foreach($parts as $part){
			                if(@ftp_chdir($this->link, $part)) continue;
			                elseif(@ftp_mkdir($this->link, $part)){
			                    ftp_chdir($this->link, $part);
			                }
			                else {
			                    $ok = false;
			                }
				        }
				        if($odir) ftp_chdir($this->link, $odir);
				        return $ok;
				    break;

			}
		}

		/**
		 * retrieves file from remote (overwrites)
		 * @param  string $remote remote file (relative to $this->path)
		 * @param  string $local  local file (must be absolute or relative to the working document)
		 * @return boolean        returns true if success, false otherwise
		 */
		function download($remote, $local) {
			if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);
			$remote = $this->get_path($remote);
			$this->last_local = $local;
			$this->last_remote = $remote;

			$ok = false;
			set_error_handler(array($this,'error_handler'),E_ALL & ~E_NOTICE);

			if(substr($local,0,7) == 'http://') {
				$tmp = array_search('uri', @array_flip(stream_get_meta_data($GLOBALS[mt_rand()]=tmpfile())));
				file_put_contents($tmp, file_get_contents($local));
				$local = $tmp;
			}
			switch($this->type) {
				case 'file':
						if(copy($remote, $local)) $ok = true;
						else return $this->throwError("file-error-downloading-from: " . $this->last_error);
					break;

				case 'ftp':
						$dir = dirname($remote);
						$odir = '';
						if($dir != '.') {
							$odir = ftp_pwd($this->link);
							ftp_chdir($this->link, $dir);
						}
						if(ftp_get($this->link, $local, basename($remote), FTP_BINARY)) $ok = true;
						if($odir) ftp_chdir($this->link, $odir);
						if(!$ok) return $this->throwError("ftp-error-downloading-from: " . $this->last_error);
					break;

				case 's3':
						try{
							$this->link->getObject(array('Bucket' => $this->bucket, 'Key' => $remote, 'SaveAs' => $local));
							$ok = true;
						}catch(\Exception $e) {
							return $this->throwError($e->getMessage());
						}
					break;
			}
			restore_error_handler();

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
		function rename($remote_source, $remote_dest, $auto_create_dirs = true, $auto_delete_dirs = true) {
			if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);
			$remote_source     = $this->get_path($remote_source);
			$remote_dest       = $this->get_path($remote_dest);
			if($remote_source == $remote_dest) return $this->throwError("files equals: [$remote_source] == [$remote_dest]");
			$this->last_remote = $remote_source;

			$ok = false;
			set_error_handler(array($this,'error_handler'),E_ALL & ~E_NOTICE);

			switch($this->type) {
				case 'file':
						if($auto_create_dirs) $this->mkdir_recursive(dirname($remote_dest));
						if(rename($remote_source, $remote_dest)) {
							$ok = true;
							if($auto_delete_dirs) $this->delete_empty_dir(dirname($remote_source));
						}
						else return $this->throwError("file-error-renaming-to: " . $this->last_error);
					break;

				case 'ftp':
						if($auto_create_dirs) $this->mkdir_recursive(dirname($remote_dest));
						if(ftp_rename($this->link, $remote_source, $remote_dest)) {
							$ok = true;
							if($auto_delete_dirs) $this->delete_empty_dir(dirname($remote_source));
						}
						else return $this->throwError("ftp-error-renaming-to: " . $this->last_error);
					break;

				case 's3':
						try{
							$this->link->copyObject(array('Bucket' => $this->bucket, 'CopySource' => urlencode($this->bucket. "/". $remote_source), 'Key' => $remote_dest, 'ACL' => self::s3_acl($auto_create_dirs)));
							// We can poll the object until it is accessible
							$this->link->waitUntilObjectExists(array('Bucket' => $this->bucket, 'Key' => $remote_dest));
							$this->link->deleteObject(array('Bucket' => $this->bucket, 'Key' => $remote_source));
							$ok = true;
						} catch(\Exception $e) {
							return $this->throwError("s3-error-renaming-to: " . $e->getMessage());
						}
					break;
			}

			restore_error_handler();

			if($ok) $this->last_remote = $remote_dest;
			return $ok;
		}

		/**
		 * retrieves filesize from remote
		 * @param  string  $remote remote file to check file size
		 * @param  boolean $force  if it is true, then will try to download the file from ftp if ftp_size fails
		 * @return int         		returns -1 on error, file size otherwise
		 */
		function size($remote_original, $force=false) {
			if(!$this->connect()) return -1;
			$remote = $this->get_path($remote_original);
			$this->last_remote = $remote;
			$size = -1;
			set_error_handler(array($this,'error_handler'),E_ALL & ~E_NOTICE);

			switch($this->type) {
				case 'file':
						if( !($size = filesize($remote)) ) $size = -1;
					break;

				case 'ftp':
						$dir = dirname($remote);
						$odir = '';
						if($dir != '.') {
							$odir = ftp_pwd($this->link);
							ftp_chdir($this->link, $dir);
						}
						$size = ftp_size($this->link, basename($remote));
						if($odir) ftp_chdir($this->link, $odir);
						if($size == -1 && $force) {
							//try to download the file and check the filesize
							$tmp = tempnam(sys_get_temp_dir(), 'file');
							if($this->download($remote_original, $tmp)) {
								if(is_file($tmp)) {
									$size = filesize($tmp);
									unlink($tmp);
								}
								else $size = -1;
							}
							else $size = -1;
						}
					break;

				case 's3':
						try {
							$info = $this->link->headObject(array('Bucket' => $this->bucket, 'Key' => $remote));
							$size = (int) $info->get('ContentLength');
						}catch(\Exception $e) {
							$size = -1;
							// return $this->throwError($e->getMessage());
						}
					break;
			}
			restore_error_handler();
			return $size;
		}

		/**
		 * retrieves file modification time from remote
		 * @param  string  $remote remote file to check file size
		 * @param  boolean $force  if it is true, then will try to download the file from ftp if ftp_size fails
		 * @return int         		returns -1 on error, file size otherwise
		 */
		function mtime($remote_original, $force=false) {
			if(!$this->connect()) return -1;
			$remote = $this->get_path($remote_original);
			$this->last_remote = $remote;
			$modified = -1;
			set_error_handler(array($this,'error_handler'),E_ALL & ~E_NOTICE);

			switch($this->type) {
				case 'file':
						if( false === ($modified = @filemtime($remote)) ) $modified = -1;
					break;

				case 'ftp':
						$dir = dirname($remote);
						$odir = '';
						if($dir != '.') {
							$odir = ftp_pwd($this->link);
							ftp_chdir($this->link, $dir);
						}
						$modified = ftp_mdtm($this->link, basename($remote));
						if($odir) ftp_chdir($this->link, $odir);
						if($modified == -1 && $force) {
							//try to download the file and check the filesize
							$tmp = tempnam(sys_get_temp_dir(), 'file');
							if($this->download($remote_original, $tmp)) {
								if(is_file($tmp)) {
									$modified = @filemtime($tmp);
									unlink($tmp);
								}
								else $modified = -1;
							}
							else $modified = -1;
						}
					break;

				case 's3':
						try {
							$info = $this->link->headObject(array('Bucket' => $this->bucket, 'Key' => $remote));
							$modified = strtotime($info->get('LastModified'));
						}catch(\Exception $e) {
							$modified = -1;
							// return $this->throwError($e->getMessage());
						}

					break;
			}
			restore_error_handler();
			return $modified;
		}

		/**
		 * Like file_get_contents()
		 * @param  string $remote remote file
		 * @return string         file raw data
		 */
		function get_contents($remote_original) {
			if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);
			$remote = $this->get_path($remote_original);
			$this->last_remote = $remote;
			$data = '';
			set_error_handler(array($this,'error_handler'),E_ALL & ~E_NOTICE);

			switch($this->type) {
				case 'file':
					$data = file_get_contents($remote);
					break;

				case 's3':
					try {
						$info = $this->link->getObject(array('Bucket' => $this->bucket, 'Key' => $remote));
						$data = $info->get('Body');
					}catch(\Exception $e) {
						return $this->throwError($e->getMessage());
					}
					break;
			}
			restore_error_handler();
			return $data;
		}

		/**
		 * Like file_put_contents (FILE_APPEND only as flag in S3, not very efficient)
 		 * @param  boolean $perms on S3 its used as a Permission control, string expecte of one of this values:
		 *                        public-read, public-read-write, authenticated-read, bucket-owner-read, bucket-owner-full-control (default public-read)
		 *
		 * @return [type] [description]
		 */
		function put_contents($remote_original, $data, $flags = 0, $perms = 'public-read') {
			if(is_array($data)) $data = implode("", $data);
			if(!$this->connect()) return $this->throwError("connect error: " . $this->last_error);
			$remote = $this->get_path($remote_original);
			$this->last_remote = $remote;
			set_error_handler(array($this,'error_handler'),E_ALL & ~E_NOTICE);

			$res = false;
			switch($this->type) {
				case 'file':
					$res = file_put_contents($remote, $data, $flags);
					break;

				case 's3':
					try {
						$body = '';
						if($flags == FILE_APPEND) {
							try {
								$info = $this->link->getObject(array('Bucket' => $this->bucket, 'Key' => $remote));
								$body = $info->get('Body');
							}catch(\Exception $e) {}
						}
						$body .= $data;
						$info = $this->link->putObject(array('Bucket' => $this->bucket, 'Key' => $remote, 'Body' => $body, 'ACL' => self::s3_acl($perms)));
						// We can poll the object until it is accessible
						$this->link->waitUntilObjectExists(array('Bucket' => $this->bucket, 'Key' => $remote));
						$res = true;
					}catch(\Exception $e) {
						return $this->throwError($e->getMessage());
					}
					break;
			}
			restore_error_handler();
			return $res;
		}

		/**
		 * retrieves a non existing name from the remote place
		 * FALTA: FTP!!
		 * @param  [type] $remote_original [description]
		 * @return [type]                  [description]
		 */
		function get_save_name($remote_original) {
			if(!$this->connect()) return false;
			if(!$remote_original) return false;
			$dir = dirname($remote_original);
			if($dir) $dir = "$dir/";
			$name = basename($remote_original);
			$name = preg_replace("/[^a-z0-9_~\.-]+/","-",strtolower(Model::idealiza($name, true)));
			$remote = $this->get_path($dir . $name);
			set_error_handler(array($this,'error_handler'),E_ALL & ~E_NOTICE);
			switch($this->type) {
				case 'file':
					while ( file_exists ( $remote )) {
						$name = preg_replace ( "/^(.+?)(_|-?)(\d*)(\.[^.]+)?$/e", "'\$1-'.(\$3+1).'\$4'", $name );
						$remote = $this->get_path($dir . $name);
					}

					break;

				case 's3':
						try {
							$prefix = preg_replace ( "/^(.+?)(_|-?)(\d*)(\.[^.]+)?$/e", "'\$1'", $name );
							$objectsIterator = $this->link->getIterator('ListObjects', array(
							    'Bucket' => $this->bucket,
							    'Prefix' => $this->get_path($dir . $prefix)
							), array(
							    'names_only' => true
							));
							$files = array();
							foreach ($objectsIterator as $key) {
							    $files[] = $key;
							}
							// echo $dir.$prefix;print_r($files);
							while ( in_array($dir . $name, $files )) {
								$name = preg_replace ( "/^(.+?)(_|-?)(\d*)(\.[^.]+)?$/e", "'\$1-'.(\$3+1).'\$4'", $name );
							}
						}catch(\Exception $e) {
							// return $this->throwError($e->getMessage());
						}
					break;
			}
			restore_error_handler();
			return $dir . $name;
		}
		/**
		 * Handle function errors
		 * */
		public function error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
			if(error_reporting() === 0) return;
			$this->last_error = "[$errno line $errline] $errstr";
			//echo "\n\n".$this->error."\n\n";
			return true;
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
