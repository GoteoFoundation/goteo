<?php

namespace Goteo\Model {

    use Goteo\Library\Text,
        Goteo\Library\FileHandler\File,
        Intervention\Image\ImageManagerStatic as ImageManager,
        Goteo\Library\Cacher;

    class Image extends \Goteo\Core\Model {

        public
			$id,
            $name,
            $type,
            $tmp,
            $error,
            $size,
            $dir_originals = 'images/', //directorio archivos originales (relativo a GOTEO_DATA_PATH o al bucket s3)
            $dir_cache = 'cache/', //directorio archivos cache
            $fallback_image = 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAC3ElEQVRYhb2XW1PiMBzF/f6POiMMBZO0KdBFZEBHnWGViKy7IIEtCmgvn+XsQ5tYLlIuZR8yfUib88v5X5qc+L6PLIfneQvPtHGStXgQBPr5XwA8z9tJMBMAtcvJZKKFkxDb2n+wA+2fj/j9Z7AAdXQHlND7dA7Oy7i9u4fnh3pu+d2jOSCenkEoA+dluJP3vcOwM4Ba+PLyCpyXQShDvd5YeSfDKgiX7A9wVW9ocUIsGMUS+v1XHZ6jJaHneRCiq8UpM2EYBLm8Ac7LmM0+di7HnQCGchyJEwuUchjFEnJ5A4RYsO0KHKeG6XS+E0QqgLJzOJJwnBooM0GIhYJxgVy+AEIscF6NoJiJVusGs9lH9g7c3t1H4pTFOy/oKlCOUMrBeRlXS0l5sANCdMFMC4QyFEsXMIolCNGFlGNQZibEqxqi0+lkA9DrvUTZHu98ueTUnG07X+GIE3Q6nx0O0Gxe62x3nNrCXBAEC2FQECpUj52ndIBNGeu6blTnBkEuX9AdL/mNbTsaglL+lQ/MXHFrowMq25OLu64LwyA4zxXgODXd85ONhjIzEo93rSA4r6Ja/bEdQFI0ubiUUtd5q3UDzw/jNpt8Z4zBYAApJYZyjKEcw7IqsO0KOC8flgNSyjjBosU+vSCG/dzwzRi2XdFh2Qsg+TNJtt2hHG8sV9/30ek8gTETlJloNq+3A0javpyUyQYUJVX4Laz7NlnIh9Ho7/YOfPcfV2GgzMTp2Tlu7+4xn3+uQAxHEvV6I2pMcWKmia8Nwbqy7IguTs/Oo07HTHBuo91+QLv9ACG6Cy5RykEoQ6/3sh/Ad/Fttx90bAmxdKmp9qtcIpRBCLHWzb0B1Oj1XnSMtbiCogzN5jX6g+FKYmYGEI0Q/f4rhOjiunWDer2B7vMvvL1NFwSPciZMu/XsczlJBVh33t+0u8yPZOt6RJrIUQ6lnuchDMO1c/tcyXYG2PfqlTb+AaY7ymbFQPTOAAAAAElFTkSuQmCC';


        private $fp,
                $cache = null;

        public static $types = array('project', 'post', 'glossary', 'info');

        // decopilatorio con los tamaños habituales para imágenes de cada entidad
        // este es el tamaño que se usa en la página de gestión de la entidad (tabla)
        // sale de buscat getLink() en toda la aplicación
        public static $sizes = array(
            'user-avatar' => '56x56c', // cabecera en la página de proyecto y perfil (hay más tamaños)
            'banner' => '700x156c',
            'call-logo' => '250x124c',
            'call-image' => '',
            'call-backimage' => '', // sistintos tamaños segun dispositivo
            'call_banner' => '',
            'call_sponsor' => '',
            'project' => '580x580',
            'post' => '500x285',
            'glossary' => '500x285',
            'info' => '500x285',
            'story' => '940x385c',
            'sponsor' => '150x85'
        );

        /**
         * Constructor.
         *
         * @param type array	$file	Array $_FILES.
         */
        public function __construct ($file = null, $name = null) {

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
            if($name) $this->name = $name;

            $this->fp = File::factory(array('bucket' => AWS_S3_BUCKET_STATIC));
            $this->fp->setPath($this->dir_originals);
            return $this;
        }

        public function setCache(Cacher $cache = null) {
            if($cache instanceOf Cacher) {
                $this->cache = $cache;
                $this->cache->setCacheGroup($this->dir_originals);
            }
            return $this;
        }

        /**
         * Sobrecarga de métodos 'getter'.
         *
         * @param type string $name
         * @return type mixed
         */
        public function __get ($name) {
            if($name == 'content') {
	            return $this->getContent();
	        }
            return $this->$name;
        }

        /**
         * (non-PHPdoc)
         * @see Goteo\Core.Model::save()
         *
         * FALTA!!!
         */
        public function save(&$errors = array(), $validate = true) {
            if(!$validate || $this->validate($errors)) {
                $this->original_name = $this->name;
                //nombre seguro
                $this->name = $this->fp->get_save_name($this->name);

                if(!empty($this->name)) {
                    $data[':name'] = $this->name;
                }

                if(!empty($this->type)) {
                    $data[':type'] = $this->type;
                }

                if(!empty($this->size)) {
                    $data[':size'] = $this->size;
                }

                try {

                    if(!empty($this->tmp)) {
                        $uploaded = $this->fp->upload($this->tmp, $this->name);

                        //@FIXME falta checkear que la imagen se ha subido correctamente
                        if (!$uploaded) {
                            $errors[] = 'fp->upload : <br />'.$this->tmp.' <br />dir: '.$this->dir_originals.' <br />file name: '.$this->name . '<br />from: '.$this->original_name . '<br />upload error: '.$this->fp->last_error;
                            return false;
                        }
                    }
                    else {
                        $errors[] = Text::get('image-upload-fail');
                        return false;
                    }

                    $this->id = $this->name;
                    $this->hash = md5($this->id);
                    $this->tmp = null;

                    return true;

            	} catch(\PDOException $e) {
                    $errors[] = 'No se ha podido guardar la imagen: ' . $e->getMessage();
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
        */
        /*
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
                $errors['image'][] = Text::get('error-image-name');
            }

            // checkeo de errores de $_FILES
            if($this->error && $this->error !== UPLOAD_ERR_OK) {
                switch($this->error) {
                    case UPLOAD_ERR_INI_SIZE:
                        $errors['image'][] = Text::get('error-image-size-too-large');
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $errors['image'][] = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $errors['image'][] = 'The uploaded file was only partially uploaded';
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        if (isset($_POST['upload']))
                            $errors['image'][] = 'No file was uploaded';
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $errors['image'][] = 'Missing a temporary folder';
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $errors['image'][] = 'Failed to write file to disk';
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $errors['image'][] = 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions';
                        break;
                    default:
                        $errors['image'][] = 'Unknown error: ' . $this->error;
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
                    $errors['image'][] = Text::get('error-image-type-not-allowed');
                }
            }
            else {
                $errors['image'][] = Text::get('error-image-type');
            }

            if(empty($this->tmp) || $this->tmp == 'none') {
                $errors['image'][] = Text::get('error-image-tmp');
            }

            if(empty($this->size)) {
                $errors['image'][] = Text::get('error-image-size');
            }

            return empty($errors);
		}

        /**
         * Imagen.
         *
         * @param type int    $id
         * @return type object    Image
         */
        static public function get($id, $default = 1)
        {

            if (empty($id))
                $id = $default;

            $image = new Image;

            // imagenes especiales
            switch ($id) {
                case '1':
                    $id = 'la_gota.png'; // imagen por defecto en toda la aplicación
                    break;
                case '2':
                    $id = 'la_gota-wof.png'; // imagen por defecto en el wall of friends
                    break;
            }

            $image->name = $id;
            $image->id = $id;
            $image->hash = md5($id);

            return $image;
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
                $sql .= ($which == 'project') ? ' ORDER BY section ASC, `order` ASC, image DESC' : ' ORDER BY image ASC';
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
		 * Para montar la url de una imagen (porque las url con parametros no se cachean bien)
		 *  - Si el thumb está creado, montamos la url de /data/cache
         *  - Sino, monamos la url de /image/
         *
		 * @param type int $id
		 * @param type int $width
		 * @param type int $height
		 * @param type int $crop
		 * @param type int $http (to force schema on the link)
		 * @return type string
		 */
		public function getLink ($width = 0, $height = 0, $crop = false, $http = false) {

            if($crop === true) $crop = 'c';
            //metodos: c (crop)
            $crop = in_array($crop, array('c')) ? $crop : '';
            $path = (int)$width . 'x' . (int)$height . $crop . '/' .$this->name;

            //Si existe la constante GOTEO_DATA_URL la usaremos en vez de SITE_URL
            if(defined('GOTEO_DATA_URL')) $link = GOTEO_DATA_URL . '/' . $path;
            else                          $link = SITE_URL . '/img/' . $path;

            if ($http && substr($link, 0, 2) == '//') {
                $link = 'http:'.$link;
            }

            return $link;
        }

        /**
         * Muestra la imagen en pantalla.
         * @param type int  $width
         * @param type int  $height
         */
        public function display ($width, $height, $crop = false) {
            $width = (int) $width;
            $height = (int) $height;
            if($this->cache) {
                if($cache_file = $this->cache->getFile($this->name, $width . 'x' . $height . ($crop ? 'c' : ''))) {
                    //correccion de extension para el cache
                    //si no la funcion save() no funciona bien

                    $info = pathinfo($cache_file, PATHINFO_EXTENSION);
                    if(!in_array($info, array('jpg', 'jpeg', 'png', 'gif'))) {
                        $cache_file = $cache_file . '.jpg';
                    }

                    header('Cache-Control: max-age=2592000');
                    //tries to flush the file and exit
                    if(Cacher::flushFile($cache_file))
                        return;
                }
            }
            $file = $this->dir_originals . $this->name;

            // Get the url file if is S3
            // TODO: more elegant solution, not mixed with assets bucket
            if($this->fp instanceOf \Goteo\Library\FileHandler\S3File) {

                $file = SRC_URL . '/' . $file;
                if(substr($file, 0, 2) === '//') {
                    $file = (HTTPS_ON ? 'https:' : 'http:' ) . $file;
                }
            }
            else {
                //Get the file by filesystem
                $file = GOTEO_DATA_PATH . '/' . $file;
            }

            // die($file);

            if($width <= 0) $width = null;
            if($height <= 0) $height = null;
            try {
                $img =  ImageManager::make($file);
                if($crop) {
                    $img->fit($width, $height, function ($constraint) {
                        $constraint->upsize();
                    });
                } else {
                    $img->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
                //store in cache if enabled
                if($this->cache && $cache_file) {
                    $img->save($cache_file);
                }

                //30days (60sec * 60min * 24hours * 30days)
                header('Cache-Control: max-age=2592000');
                //flush data
                echo $img->response();

            }catch(\Exception $e) {
                //Shows a fallback image with the error message
                try {
                    $msg = $e->getMessage();
                    $w = $width ? $width : 32;
                    $h = $height ? $height : 32;

                    //flush data
                    echo $img =  ImageManager::canvas($w, $h, '#DCDCDC')
                                 ->insert($this->fallback_image, 'center')
                                 ->text($msg, round($w/2), round($h/2), function($font){
                                    $font->align('center');
                                    $font->valign('middle');
                                    $font->color('#666666');
                                 })
                                 ->response('png');
                }

                catch(\Exception $e) {
                    //if the fallback image fails, what can i do?
                    die($e->getMessage());
                }
            }
		}

        /**
         * Passthru a file with content-type, name
         * @param  [type] $file [description]
         * @return [type]       [description]
         */
        static function stream($file, $exit = true) {
            //redirection if is http stream
            if(substr($file,0,2) == '//') $file = (HTTPS_ON ? 'https:' : 'http:') . $file;
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

        private function getContent () {
            return file_get_contents($this->name);
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

        /**
         *  Get a valid gallery for a generic Model
         *  Para proyecto hay que usar Project\Image::getList  por el tema de secciones y
         *
         *
         * @param  string       $model_table    entity
         * @param  varchar(50)  $model_id  entity item id  user | project | post | info | glossary
         */
        public static function getModelGallery($model_table, $model_id) {
            $gallery = [];

            if (!is_string($model_table) || !in_array($model_table, self::$types)) {
                return $gallery;
            }

            try {
                $sql = "SELECT image FROM {$model_table}_image WHERE {$model_table} = ?";
                if ($model_table === 'project') $sql .= ' ORDER BY section ASC, `order` ASC';
                $query = self::query($sql, array($model_id));
                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $image) {
                    $gallery[] = self::get($image->image);
                }

            } catch(\PDOException $e) {
                //
            }
            return $gallery;
        }

        /**
         * Get the precalculated image for a Model o from the gallery
         */
        public static function getModelImage($image, Array $gallery = []) {

            if($image instanceOf Image && $image->id) {
                return $image;
            }
            if ($image && $image !== 'empty') {
                return self::get($image);
            }
            if(count($gallery) > 0) {
                if($gallery[0] instanceOf Image) {
                    return $gallery[0];
                }
                return self::get($gallery[0]);
            }
            return null;
        }
        /**
         * Add current image to a Model gallery
         * @param string $model_table The Model table (post, glossary, project, etc)
         * @param string/integer $model_id    the ID of the Model
         */
        public function addToModelGallery($model_table, $model_id) {
           if (!is_string($model_table) || !in_array($model_table, self::$types)) {
                return false;
            }
            $ok = !empty($this->id);
            if($this->tmp && $this->name) $ok = $this->save();
            if($ok) {
                try {
                    self::query("INSERT INTO {$model_table}_image ({$model_table}, image) VALUES (:id, :image)", array(':id' => $model_id, ':image' => $this->id));
                } catch(\PDOException $e) {
                    //
                    return false;
                }

            }
            return $ok;
        }

        /**
         * Sets the current image to a Model main image
         * @param string $model_table The Model table (post, glossary, project, etc)
         * @param string/integer $model_id    the ID of the Model
         */
        public function setModelImage($model_table, $model_id) {
           if (!is_string($model_table) || !in_array($model_table, self::$types)) {
                return false;
            }
            $ok = !empty($this->id);
            if($this->tmp && $this->name) $ok = $this->save();
            if($ok) {
                try {
                    $sql = "UPDATE `$model_table` SET image = :image WHERE id = :id";
                    self::query($sql, array(':image'=>$this->id, ':id'=>$model_id));
                } catch(\PDOException $e) {
                    //
                    return false;
                }
            }
            return $ok;
        }

        /**
         * deletes the main image from a Model gallery
         * @param string $model_table The Model table (post, glossary, project, etc)
         * @param string/integer $model_id    the ID of the Model
         */
        public static function deleteModelImage($model_table, $model_id) {
            if (!is_string($model_table) || !in_array($model_table, self::$types)) {
                return false;
            }
            try {
                $sql = "UPDATE `$model_table` SET image = :image WHERE id = :id";
                self::query($sql, array(':image'=>'', ':id'=>$model_id));
            } catch(\PDOException $e) {
                //
                return false;
            }
        }

        /**
         * Quita una imagen de la tabla de relaciones y de la tabla de imagenes
         *
         * @param  string       $which    'project', 'post', 'glossary', 'info'
         * @return bool        true|false
         *
         */
        public function remove(&$errors = array(), $model_table = null) {

            /*
            NOTA: El borrado de archivos no debe hacerse aqui pues en casos de sistemas
                  distribuidos puede haber problemas porque las instancias web pueden no tener
                  el cache generado.
                  Otro problema (sobretodo si se usan CDN) es la cache de proxy sobre los archivos generados

            @FIXME: crear un script en cron que repase todas las tablas con imagenes y borre
                    del disco y el cache:

                    //borrado disco:
                    $this->fp->delete($this->id);

                    //borrado cache (hack horrible por mejorar):
                    $c = new Cache($this->dir_cache);
                    $c->rm('*\/' . $this->name);

             */
            // no borramos nunca la imagen de la gota
            if ($this->id == 'la_gota.png') return false;

            try {
                if (is_string($model_table) && in_array($model_table, self::$types)) {

                    $sql = "SELECT `{$model_table}` FROM {$model_table}_image WHERE image = ?";
                    $query = self::query($sql, array($this->id));
                    $model_id = $query->fetchColumn();

                    if($model_id) {
                        $sql = "DELETE FROM {$model_table}_image WHERE image = ?";
                        $query = self::query($sql, array($this->id));
                        // Actualiza el campo con uno de la galeria
                        if($gallery = self::getModelGallery($model_table, $model_id)) {
                            $gallery[0]->setModelImage($model_table, $model_id);
                        }
                        else {
                            self::deleteModelImage($model_table, $model_id);
                        }
                    }
                    else {
                        $errors[] = "{$this->id} not found in {$model_table}_image";
                    }
                }
            } catch(\PDOException $e) {
                $errors[] = $e->getMessage();
                // aquí debería grabar en un log de errores o mandar un mail a GOTEO_FAIL_MAIL
                return false;
            }
            $this->id = 1;
            return true;
        }
	}

}
