<?php

namespace Goteo\Model {

    use Goteo\Library\Text,
        Goteo\Library\File,
        Goteo\Library\MImage,
        Goteo\Library\Cache;

    class Image extends \Goteo\Core\Model {

        public
			$id,
            $name,
            $type,
            $tmp,
            $error,
            $size,
            $dir_originals = 'images/', //directorio archivos originales (relativo a data/ o al bucket s3)
            $dir_cache = 'cache/'; //directorio archivos cache (relativo a data/ o al bucket s3)
        private $fp;

        public static $types = array('user','project', 'post', 'glossary', 'info');

        /**
         * Constructor.
         *
         * @param type array	$file	Array $_FILES.
         */
        public function __construct ($file = null) {

            if(is_array($file)) {
                $this->name = $file['name'];
                $this->type = $file['type'];
                $this->tmp = $file['tmp_name'];
                $this->error = $file['error'];
                $this->size = $file['size'];
            }
            elseif(is_string($file)) {
				$this->name = basename($file);
				$this->tmp = $file;
			}

            $this->fp = File::get();
        }

        /**
         * Sobrecarga de métodos 'getter'.
         *
         * @param type string $name
         * @return type mixed
         */
        public function __get ($name) {
            if($name == "content") {
	            return $this->getContent();
	        }
            return $this->$name;
        }

        /**
         * retorna nombre "seguro", que no existe ya
         */
        public function save_name() {
            //un nombre que no exista
            $name = $this->fp->get_save_name($this->dir_originals . $this->name);
            if($this->dir_originals) $name = substr($name, strlen($this->dir_originals));
            return $name;
        }

        /**
         * Retorna URL del archivo o ruta absluta si es local
         */
        public function url( $path = null) {
            if($path === null) $path = $this->dir_originals . $this->name;
             //url del archivo o ruta absoluta si es local
            if($this->fp->type == 'file') {
                $url = $this->fp->get_path($path);
            }
            else $url = SRC_URL . "/" . $path;

            return $url;
        }

        /**
         * (non-PHPdoc)
         * @see Goteo\Core.Model::save()
         *
         * FALTA!!!
         */
        public function save(&$errors = array()) {
            if($this->validate($errors)) {
                //nombre seguro
                $name = $this->save_name();

                if(!empty($this->name)) {
                    $data[':name'] = $name;
                }

                if(!empty($this->type)) {
                    $data[':type'] = $this->type;
                }

                if(!empty($this->size)) {
                    $data[':size'] = $this->size;
                }

                // die($name);
                if(!empty($this->tmp)) {
                    $this->fp->upload($this->tmp, $this->dir_originals . $name);
                }
                else {
                    $errors[] = Text::get('image-upload-fail');
                    return false;
                }

                try {

                    // Construye SQL.
                    $query = "REPLACE INTO image (";
                    foreach($data AS $key => $row) {
                        $query .= substr($key, 1) . ", ";
                    }
                    $query = substr($query, 0, -2) . ") VALUES (";
                    foreach($data AS $key => $row) {
                        $query .= $key . ", ";
                    }
                    $query = substr($query, 0, -2) . ")";
                    // Ejecuta SQL.
                    $result = self::query($query, $data);
                    if(empty($this->id)) $this->id = self::insertId();
                    $this->name = $name;
                    return true;
            	} catch(\PDOException $e) {
                    $errors[] = "No se ha podido guardar el archivo en la base de datos: " . $e->getMessage();
                    return false;
    			}
            }
            return false;
		}

		/**
		* Returns a secure name to store in file system, if the generated filename exists returns a non-existing one
		* @param $name original name to be changed-sanitized
		* @param $dir if specified, generated name will be changed if exists in that dir
* Esto ya lo hace la clase File con get_save_name
		public static function check_filename($name='',$dir=null){
			$name = preg_replace("/[^a-z0-9_~\.-]+/","-",strtolower(self::idealiza($name, true)));
			if(is_dir($dir)) {
				while ( file_exists ( "$dir/$name" )) {
					$name = preg_replace ( "/^(.+?)(_?)(\d*)(\.[^.]+)?$/e", "'\$1_'.(\$3+1).'\$4'", $name );
				}
			}
			return $name;
		}
		*/

		/**
		 * (non-PHPdoc)
		 * @see Goteo\Core.Model::validate()
		 */
		public function validate(&$errors = array()) {
            
			if(empty($this->name)) {
                $errors['image'] = Text::get('error-image-name');
            }
            
            // checkeo de errores de $_FILES
            if($this->error !== UPLOAD_ERR_OK) {
                switch($this->error) {
                    case UPLOAD_ERR_INI_SIZE:
                        $errors['image'] = Text::get('error-image-size-too-large');
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $errors['image'] = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $errors['image'] = 'The uploaded file was only partially uploaded';
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        if (isset($_POST['upload']))
                            $errors['image'] = 'No file was uploaded';
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $errors['image'] = 'Missing a temporary folder';
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $errors['image'] = 'Failed to write file to disk';
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $errors['image'] = 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions';
                        break;
                }
                return false;
            }

            if(!empty($this->type)) {
                $allowed_types = array(
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'image/svg+xml',
                );
                if(!in_array($this->type, $allowed_types)) {
                    $errors['image'] = Text::get('error-image-type-not-allowed');
                }
            }
            else {
                $errors['image'] = Text::get('error-image-type');
            }

            if(empty($this->tmp) || $this->tmp == "none") {
                $errors['image'] = Text::get('error-image-tmp');
            }

            if(empty($this->size)) {
                $errors['image'] = Text::get('error-image-size');
            }
            
            return empty($errors);
		}

		/**
		 * Imagen.
		 *
		 * @param type int	$id
		 * @return type object	Image
		 */
	    static public function get ($id) {
            try {
                $query = static::query("
                    SELECT
                        id,
                        name,
                        type,
                        size
                    FROM image
                    WHERE id = :id
                    ", array(':id' => $id));
                $image = $query->fetchObject(__CLASS__);
                return $image;
            } catch(\PDOException $e) {
                return false;
            }
		}

        /**
         * Galeria de imágenes de un usuario / proyecto
         *
         * @param  varchar(50)  $id    user id |project id
         * @param  string       $which    'user'|'project'
         * @return mixed        false|array de instancias de Image
         */
        public static function getAll ($id, $which) {

            if (!\is_string($which) || !\in_array($which, self::$types)) {
                return false;
            }

            $gallery = array();

            try {
                $sql = "SELECT image FROM {$which}_image WHERE {$which} = ?";
                $sql .= ($which == 'project') ? " ORDER BY section ASC, `order` ASC, image DESC" : " ORDER BY image ASC";
                $query = self::query($sql, array($id));
                foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $image) {
                    $gallery[] = self::get($image['image']);
                }

                return $gallery;
            } catch(\PDOException $e) {
                return false;
            }

        }

        /**
         * Quita una imagen de la tabla de relaciones y de la tabla de imagenes
         *
         * @param  string       $which    'user'|'project'|'post'
         * @return bool        true|false
         *
         */
        public function remove($which) {

            try {
                self::query("START TRANSACTION");
                $sql = "DELETE FROM image WHERE id = ?";
                $query = self::query($sql, array($this->id));

                // para usuarios y proyectos que tienen N imagenes
                // por ahora post solo tiene 1
                if (\is_string($which) && \in_array($which, self::$types)) {
                    $sql = "DELETE FROM {$which}_image WHERE image = ?";
                    $query = self::query($sql, array($this->id));
                }
                self::query("COMMIT");

                //esborra de disk
                $this->fp->delete($this->dir_originals . $this->name);

                return true;
            } catch(\PDOException $e) {
                return false;
            }
        }


		/**
		 * Para montar la url de una imagen (porque las url con parametros no se cachean bien)
		 *  - Si el thumb está creado, montamos la url de /data/cache
         *  - Sino, monamos la url de /image/
         *
		 * @param type int $id
		 * @param type int $width
		 * @param type int $height
		 * @param type int $crop
		 * @return type string
		 */
		public function getLink ($width = 'auto', $height = 'auto', $crop = false) {

            $tc = ($crop ? "c" : "");
            $cache = "{$width}x{$height}{$tc}/{$this->name}";
            
            $c = new Cache($this->dir_cache, $this->fp);
            
            if($c->get_file($cache)) {
                return SRC_URL . "/cache/{$cache}";
            } else {
                return SRC_URL . "/image/{$this->id}/{$width}/{$height}/" . $crop;
            }

		}

		/**
		 * Carga la imagen en el directorio temporal del sistema.
		 *
		 * @return type bool
		 */
		public function load () {
		    if(!empty($this->id) && !empty($this->name)) {
    		    $tmp = tempnam(sys_get_temp_dir(), 'Goteo');
                $file = fopen($tmp, "w");
                fwrite($file, $this->content);
                fclose($file);
                if(!file_exists($tmp)) {
                    throw \Goteo\Core\Exception("Error al cargar la imagen temporal.");
                }
                else {
                    $this->tmp = $tmp;
                    return true;
                }
		    }
		}

		/**
		 * Elimina la imagen temporal.
		 *
		 * @return type bool
		 */
    	public function unload () {
    	    if(!empty($this->tmp)) {
                if(!file_exists($this->tmp)) {
                    throw \Goteo\Core\Exception("Error, la imagen temporal no ha sido encontrada.");
                }
                else {
                    unlink($this->tmp);
                    unset($this->tmp);
                    return true;
                }
    	    }
    	    return false;
		}

		/**
		 * Muestra la imagen en pantalla.
		 * @param type int	$width
		 * @param type int	$height
		 */
        public function display ($width, $height, $crop) {

            $cache = $width."x$height" . ($crop ? "c" : "") . "/" . $this->name;

			$url_cache = $this->url($this->dir_cache . $cache);

            $url_original = $this->url();

            $c = new Cache($this->dir_cache, $this->fp);

            ignore_user_abort(true);
            //comprueba si existe el archivo en cache
            if($c->get_file($cache)) {
                //si existe, servimos el fichero inmediatamante (via redireccion http)
                //PERO continuamos la ejecuciÃ³n del script para recrear el cache si estÃ¡ expirado
                ob_end_clean();
                header("Connection: close", true);
                self::stream($url_cache, false);
                //close connection with browser
                ob_end_flush();
                flush();
                //check if file is newer
                if(!$c->expired($cache, $this->fp->mtime($this->name))) {
                    exit;
                }
                //continue to force rebuild cache

            }
            //si no existe o es nuevo, creamos el archivo
            $im = new MImage($url_original);
            $im->fallback('auto');
            $im->proportional($crop ? 1 : 2);
            $im->quality(98);

            $im->resize($width, $height);

            //guardar a cache si no hay errores
            if(!$im->has_errors()) {
                //guardar un archivo temporal y subir
                $tmp = tempnam(sys_get_temp_dir(), 'goteo-img');
                $im->save($tmp);
                //subir el archivo a cache
                $c->put_file($tmp, $cache);
                unlink($tmp);
            }

            ignore_user_abort(false);

            //stream del archivo creado y muerte del script
            $im->flush();
		}

        /**
         * Passthru a file with content-type, name
         * @param  [type] $file [description]
         * @return [type]       [description]
         */
        static function stream($file, $exit = true) {
            //redirection if is http stream
            if(substr($file, 0 , 7) == 'http://' || substr($file, 0 , 8) == 'https://') {
                header("Location: $file");
            }
            else {
                list($width, $height, $type, $attr) = @getimagesize( $file );
                if(!$type && function_exists( 'exif_imagetype' ) ) {
                    $type = exif_imagetype($file);
                }
                if($type) {
                     $type = image_type_to_mime_type($type);
                }
                else {
                    $type = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    die($type);
                    if($type == 'jpg') $type = "jpeg";
                    if(!in_array($type, array('jpeg', 'png', 'gif'))) die("file $type not image!");
                    $type = "image/$type";
                }

                header("Content-type: " . $type);
                header('Content-Disposition: inline; filename="' . str_replace("'", "\'", basename($file)) . '"');
                header("Content-Length: " . @filesize($file));
                readfile($file);
            }
            if($exit) exit;
        }

// POSIBLEMENTE código obsoleto a partir de este punto
    	public function isJPG () {
		    return ($this->type == 'image/jpg') || ($this->type == 'image/jpeg');
		}

    	public function isPNG () {
		    return ($this->type == 'image/png');
		}

    	public function toGIF () {
    	    $this->load();
    	    if(!$this->isGIF()) {
                list($width, $height, $type) = getimagesize($this->tmp);
                switch($type) {
                	case 1:
                		$image = imagecreatefromgif($this->tmp);
                		break;
                	default:
                	case 2:
                		$image = imagecreatefromjpeg($this->tmp);
                		break;
                	case 3:
                		$image = imagecreatefrompng($this->tmp);
                		break;
                	case 6:
                		$image = imagecreatefromwbmp($this->tmp);
                		break;
                }
                $tmp = static::replace_extension($this->tmp, 'gif');
                $this->unload();
                $this->tmp = $tmp;
           		imagegif($image, $this->tmp);
           		imagedestroy($image);
                return true;
    	    }
    	    return;
    	}

        public function toJPG () {
    	    $this->load();
    	    if(!$this->isJPG()) {
                list($width, $height, $type) = getimagesize($this->tmp);
                switch($type) {
                	case 1:
                		$image = imagecreatefromgif($this->tmp);
                		break;
                	default:
                	case 2:
                		$image = imagecreatefromjpeg($this->tmp);
                		break;
                	case 3:
                		$image = imagecreatefrompng($this->tmp);
                		break;
                	case 6:
                		$image = imagecreatefromwbmp($this->tmp);
                		break;
                }
                $tmp = static::replace_extension($this->tmp, 'gif');
                $this->unload();
                $this->tmp = $tmp;
           		imagejpeg($image, $this->tmp, 100);
           		imagedestroy($image);
                return true;
    	    }
    	    return;
    	}

    	public function toPNG () {
    	    $this->load();
    	    if(!$this->isPNG()) {
                list($width, $height, $type) = getimagesize($this->tmp);
                switch($type) {
                	case 1:
                		$image = imagecreatefromgif($this->tmp);
                		break;
                	default:
                	case 2:
                		$image = imagecreatefromjpeg($this->tmp);
                		break;
                	case 3:
                		$image = imagecreatefrompng($this->tmp);
                		break;
                	case 6:
                		$image = imagecreatefromwbmp($this->tmp);
                		break;
                }
                $tmp = static::replace_extension($this->tmp, 'gif');
                $this->unload();
                $this->tmp = $tmp;
           		imagepng($image, $this->tmp, 100);
           		imagedestroy($image);
                return true;
    	    }
    	    return;
    	}

        private function getContent () {
            return file_get_contents($this->dir_originals . $this->name);
    	}

        /*
         * Devuelve la imagen en GIF.
         *
         * @return type object	Image
         */
        static public function gif ($id) {
            $img = static::get($id);
            if(!$img->isGIF())
                $img->toGIF();
            return $img;
        }

        /*
         * Devuelve la imagen en JPG/JPEG.
         *
         * @return type object	Image
         */
        static public function jpg ($id) {
            $img = static::get($id);
            if ($img->isJPG())
                $img->toJPG();
            return $img;
        }

        /*
         * Devuelve la imagen en PNG.
         *
         * @return type object	Image
         */
        static public function png ($id) {
            $img = self::get($id);
            if ($img->isPNG())
                $img->toPNG();
            return $img;
        }

        /**
         * Reemplaza la extensión de la imagen.
         *
         * @param type string	$src
         * @param type string	$new
         * @return type string
         */
    	static private function replace_extension($src, $new) {
    	    $pathinfo = pathinfo($src);
    	    unset($pathinfo["basename"]);
    	    unset($pathinfo["extension"]);
    	    return implode(DIRECTORY_SEPARATOR, $pathinfo) . '.' . $new;
    	}

	}

}
