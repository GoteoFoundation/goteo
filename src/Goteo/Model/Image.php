<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

namespace Goteo\Model;

use Goteo\Library\Text;
use Goteo\Library\FileHandler\File;
use Intervention\Image\ImageManagerStatic as ImageManager;
use Goteo\Library\Cacher;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use Goteo\Application\Config;
use Goteo\Application\Exception\ModelException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class Image extends \Goteo\Core\Model {

    public
		$id,
        $name,
        $type,
        $tmp,
        $error,
        $size,
        $quality = 92,
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
        if($file instanceOf Image) {
            $this->name = $file->name;
            $this->type = $file->type;
            $this->tmp = $file->tmp;
            $this->error = $file->error;
            $this->size = $file->size;
        }
        elseif($file instanceOf UploadedFile) {
            try {
                $this->error = $file->getError();
                $this->name = $file->getClientOriginalName();
                $this->tmp = $file->getPathName();
                $this->type = $file->getMimeType();
                $this->size = $file->getSize();
            } catch(FileNotFoundException $e) {
            }
        }
        elseif(is_array($file)) {
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

            try {

                if($this->isUploadedFile()) {
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

    static public function getUploadErrorText($error) {
        if(!$error || $error === UPLOAD_ERR_OK) return '';
        switch($error) {
            case UPLOAD_ERR_INI_SIZE:
                return Text::get('error-image-size-too-large');
                break;
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                break;
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
                break;
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
                break;
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions';
                break;
            default:
                return 'Unknown error: ' . $error;
        }
    }

    public function getUploadError() {
        return self::getUploadErrorText($this->error);
    }

    /**
    * Detects max size of file cab be uploaded to server
    *
    * Based on php.ini parameters "upload_max_filesize", "post_max_size" &
    * "memory_limit". Valid for single file upload form. May be used
    * as MAX_FILE_SIZE hidden input or to inform user about max allowed file size.
    *
    * @return int Max file size in bytes
    * From: http://www.kavoir.com/2010/02/php-get-the-file-uploading-limit-max-file-size-allowed-to-upload.html
    */
    static public function getSystemMaxFileSize($units = 'bytes') {
        /**
        * Converts shorthands like "2M" or "512K" to bytes
        *
        * @param $size
        * @return mixed
        */
        $normalize = function($size) {
            if (preg_match('/^([\d\.]+)([KMG])$/i', $size, $match)) {
                $pos = array_search($match[2], array("K", "M", "G"));
                if ($pos !== false) {
                    $size = $match[1] * pow(1024, $pos + 1);
                }
            }
            return $size;
        };
        $max_upload = $normalize(ini_get('upload_max_filesize'));
        $max_post = $normalize(ini_get('post_max_size'));
        $memory_limit = $normalize(ini_get('memory_limit'));
        $maxFileSize = min($max_upload, $max_post, $memory_limit);
        $div = 1;
        if($units == 'kb') $div = 1024;
        elseif($units == 'mb') $div = 1024 * 1024;
        return round($maxFileSize / $div);
    }

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
            $errors['image'][] = self::getUploadErrorText($this->error);
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

        if($id instanceOf Image) {
            return $id;
        }

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

    public function isUploadedFile() {
        return !empty($this->tmp) && !empty($this->size);
    }

    public function getName() {
        return $this->name;
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
            $link = (Config::get('ssl') ? 'https:' : 'http:').$link;
        }

        return $link;
    }

    /**
     * Returns the type of the file (or the extension if not defined)
     */
    public function getType() {
        if(!$this->type) {
            if(strpos($this->getName(), '.') !== false)
                $this->type = pathinfo($this->getName(), PATHINFO_EXTENSION);
            if(empty($this->type))
                $this->type = 'bin';
        }
        return $this->type;
    }

    /**
     * Muestra la imagen en pantalla.
     * @param type int  $width
     * @param type int  $height
     */
    public function display ($width, $height, $crop = false) {
        $width = (int) $width;
        $height = (int) $height;

        $file = $this->dir_originals . $this->name;
        // Get the url file if is S3
        // TODO: more elegant solution, not mixed with assets bucket
        if($this->fp instanceOf \Goteo\Library\FileHandler\S3File) {

            $file = SRC_URL . '/' . $file;
            if(substr($file, 0, 2) === '//') {
                $file = (Config::get('ssl') ? 'https:' : 'http:' ) . $file;
            }
        }
        else {
            //Get the file by filesystem
            $file = GOTEO_DATA_PATH . $file;
        }
        // die($file);

        // Avoid resize on GIF images
        if('gif' == pathinfo($this->name, PATHINFO_EXTENSION)) {
            if($ret = @file_get_contents($file)) {
                return $ret;
            }
        }

        // Retrieve the chachec version if exists
        if($this->cache && $this->name) {
            if($cache_file = $this->cache->getFile($this->name, $width . 'x' . $height . ($crop ? 'c' : ''))) {
                //correccion de extension para el cache
                //si no la funcion save() no funciona bien
                // die("[$cache_file");
                $info = pathinfo($cache_file, PATHINFO_EXTENSION);
                if(!in_array($info, array('jpg', 'jpeg', 'png', 'gif'))) {
                    $cache_file = $cache_file . '.jpg';
                }

                //returns the content of the file
                if($ret = @file_get_contents($cache_file)) {
                    return $ret;
                }
            }
        }



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

        } catch(\Exception $e) {
            //Shows a fallback image with the error message
            $msg = $e->getMessage();
            $w = $width ? $width : 32;
            $h = $height ? $height : 32;
            $this->error = 'not_found';
            //flush data
            $img =  ImageManager::canvas($w, $h, '#DCDCDC')
                         ->insert($this->fallback_image, 'center')
                         ->text($msg, round($w/2), round($h/2), function($font){
                            $font->align('center');
                            $font->valign('middle');
                            $font->color('#666666');
                         });
        }
        //flush data
        $img->encode($image->mime, $this->quality);
        return $img->getEncoded();
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
            $sql = "SELECT image FROM `{$model_table}_image` WHERE {$model_table} = ?";
            if ($model_table === 'project') $sql .= ' ORDER BY section ASC, `order` ASC';
            else $sql .= ' ORDER BY `order` ASC';
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
                self::query("INSERT INTO `{$model_table}_image` (`{$model_table}`, image) VALUES (:id, :image)", array(':id' => $model_id, ':image' => $this->id));
            } catch(\PDOException $e) {
                //
                return false;
            }

        }
        return $ok;
    }

    /**
     * deletes and main image from a Model gallery
     * @param string $model_table The Model table (post, glossary, project, etc)
     * @param string/integer $model_id    the ID of the Model
     */
    public function delFromModelGallery($model_table, $model_id) {
        if (!is_string($model_table) || !in_array($model_table, self::$types)) {
            return false;
        }
        try {
            $values = array(':image'=> $this->id, ':id' => $model_id);
            $sql = "DELETE FROM `{$model_table}_image` WHERE `{$model_table}` = :id AND image = :image";
            // die(\sqldbg($sql, $values));
            self::query($sql, $values);
        } catch(\PDOException $e) {
            //
            return false;
        }
        return true;
    }


    /**
     * Removes the current gallery and puts the new one
     * @param string $model_table The Model table (post, glossary, project, etc)
     * @param string/integer $model_id    the ID of the Model
     */
    public static function replaceGallery($model_table, $model_id, array $gallery) {
        if (!is_string($model_table) || !in_array($model_table, self::$types)) {
            return false;
        }
        try {
            $values = array(':id' => $model_id);
            $ids = [];
            $inserts = [];
            $orders = [];
            $order_values = [];
            $index = 0;
            foreach($gallery as $i => $img) {
                $ok = !empty($img->name);
                if($img->tmp && $img->name) $ok = $img->save($errors);
                if($ok) {
                    $values[":name_$i"] = $img->id ? $img->id : $img->name;
                    $order_values[":order_$i"] = $index++;
                    $ids[] = ":name_$i";
                    $orders[] = "`order` = :order_$i";
                    $inserts[] = "(:id, :name_$i, :order_$i)";
                } else {
                    // print_r($img);print_r($errors);die;
                    throw new ModelException($img->name . ': ' . implode(", ", $errors['image']));
                }
            }
            $sql = "DELETE FROM `{$model_table}_image` WHERE `{$model_table}` = :id AND `image` NOT IN (" . implode(", ", $ids) . ")";
            self::query($sql, $values);
            $sql = "REPLACE `{$model_table}_image` (`{$model_table}`, `image`, `order`) VALUES " . implode(", ", $inserts);
            // die(\sqldbg($sql, $values + $order_values));
            self::query($sql, $values + $order_values);
        } catch(\PDOException $e) {
            throw new ModelException($e->getMessage());
            // return false;
        }
        return true;
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
                self::query($sql, array(':image'=>$this->id, ':id' => $model_id));
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
            $values = array(':image'=>'', ':id' => $model_id);
            $sql = "UPDATE `$model_table` SET image = :image WHERE id = :id";
            // die(\sqldbg($sql, $values));
            self::query($sql, $values);
        } catch(\PDOException $e) {
            //
            return false;
        }
        return true;
    }


    /**
     * Converts this instance to a
     * Symfony\Component\HttpFoundation\File
     * @return [type] [description]
     */
    public function toSymfonyFile() {
        return new SymfonyFile($this->name, false);
    }

    public function __toString() {
        return $this->getName();
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
        if ($this->id == 'la_gota.png') {
            $errors[] = 'Default image cannot be deleted';
            return false;
        }

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
            // aquí debería grabar en un log de errores o mandar un mail a Config::getMail('fail')
            return false;
        }
        $this->id = 1;
        return true;
    }
}

