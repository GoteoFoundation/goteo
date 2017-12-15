<?php

namespace Goteo\Model\Contract;

use Goteo\Application\App;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Library\Check;
use Goteo\Library\FileHandler\File;
use Goteo\Library\Text;
use Goteo\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
// use Goteo\Application\Config;
// use Goteo\Application\Exception\ModelException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class Document extends \Goteo\Core\Model {

    public
        $id,
        $contract,
        $name,
        $type,
        $size,
        $tmp,
        $filedir,
        $dir = 'contracts/';
    private $fp;

    /**
     * Constructor.
     *
     * @param type array	$file	Array $_FILES.
     */
    public function __construct ($file = null) {
        if($file instanceOf Document) {
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
                if($this->type === 'application/octet-stream')
                    $this->type = $file->getClientMimeType();

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
        }
        if($name) $this->name = $name;

        $this->fp = File::factory(array('bucket' => AWS_S3_BUCKET_DOCUMENT));
        $this->fp->setPath($this->dir);
    }

	/**
	 * (non-PHPdoc)
	 * @see Goteo\Core.Model::validate()
	 */
	public function validate(&$errors = array()) {
		if(empty($this->name)) {
            $errors[] = 'Sin nombre de archivo';
        }
		if(empty($this->contract)) {
            $errors[] = 'Sin id de proyecto/contrato['.$this->contract;
        }

        // checkeo de errores de $_FILES
        if($this->error && $this->error !== UPLOAD_ERR_OK) {
            $errors['image'][] = Model\Image::getUploadErrorText($this->error);
            return false;
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
     * Solo graba, no actualiza
     * (non-PHPdoc)
     * @see Goteo\Core.Model::save()
     */
    public function save(&$errors = array())
    {
        if ($this->validate($errors)) {
            $this->original_name = $this->name;

            // verificar que existe el directorio para documentos de este proyecto

            $this->filedir = $this->contract.'/';
            $this->name = $this->fp->get_save_name($this->filedir . $this->name);

            $data = array(
                ':contract' => $this->contract,
                ':name' => $this->name,
                ':type' => $this->type,
                ':size' => $this->size,

            );

            try {

                //si es un archivo que se sube
                if ($this->isUploadedFile()) {
                    //subimos archivo con permisos privados
                    $uploaded = $this->fp->upload($this->tmp, $this->filedir . $this->name, array('auto_create_dirs' => true, 'perms' => 'bucket-owner-full-control'));

                    //@FIXME falta checkear que la imagen se ha subido correctamente
                    if (!$uploaded) {
                        $errors[] = 'fp->upload : <br />'.$this->tmp.' <br />dir: '.$this->dir.'  '.$this->filedir.' <br />file name: '.$this->name . '<br />from: '.$this->original_name;
                        return false;
                    }
                } else {
                    $errors[] = Text::get('error-image-tmp');
                    return false;
                }

                // Construye SQL.
                $query = "INSERT INTO document (contract, name, type, size)
                    VALUES (:contract, :name, :type, :size)";
                // Ejecuta SQL.
                if (self::query($query, $data)) {
                    $this->id = self::insertId();
                    return true;
                } else {
                    $errors[] = "Fallo sql: $query " . print_r($data, true);
                    return false;
                }
            } catch (\PDOException $e) {
                $errors[] = "No se ha podido guardar el archivo en la base de datos: " . $e->getMessage();
                return false;
            }
        }

        return false;

    }

    public function isUploadedFile() {
        return !empty($this->tmp);
    }

    public function getName() {
        return $this->name;
    }

    /**
     * Converts this instance to a
     * Symfony\Component\HttpFoundation\File
     * @return [type] [description]
     */
    public function toSymfonyFile() {
        return new SymfonyFile($this->name, false);
    }

    public function getLink ($http = false) {

        $link = '/document/' . $this->id . '/' . $this->name;

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
                $this->type = 'application/octet-stream';
        }

        return end(explode('/', $this->type));
    }

    /**
     * Get documentdata
     * @param varcahr(50) $id  Document identifier
     * @return object instanceof Document or false if it doesn't exist
     */
 	public static function get ($id) {

        try {
            $sql = "SELECT *
                FROM document
                WHERE id = :id";

            $query = static::query($sql, array(':id' => $id));
            if($doc = $query->fetchObject(__CLASS__)) {
                $doc->filedir = $doc->dir . $doc->contract . '/';
                return $doc;
            }

        } catch(\PDOException $e) {
            throw new ModelNotFoundException($e->getMessage());
        }
		throw new ModelNotFoundException('Document not found!');
	}

    /**
     * Get the documents for a contract
     * @param varcahr(50) $id  Contract identifier
     * @return array of documents or false if it doesn't exist
     */
 	public static function getDocs ($id) {

        $array = array ();
        try {
            $values = array(':id' => $id);

            $sql = "SELECT *
                FROM document
                WHERE contract = :id
                ORDER BY id DESC";

            $query = static::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $document) {
                $document->filedir = $document->dir . $document->contract . '/';
                $array[] = $document;
            }

            return $array;
        } catch(\PDOException $e) {
			throw new ModelNotFoundException($e->getMessage());
        }
	}

    /*
     * Elimina el registro y el archivo
     * TODO: ROLLBACK
     */
    public function remove (&$errors = array()) {
        $ok = false;

        try {
            $sql = "DELETE FROM document WHERE id = ?";
            $values = array($this->id);

            if (self::query($sql, $values)) {
                 //esborra de disk
                if ($this->fp->delete($this->contract . '/' . $this->name)) {
                    $ok = true;
                } else {
                    $errors[] = "File deleted from database successfully. But failed deleting it from disk.";
                }
            } else {
                $errors[] = "Document [{$this->id}] not deleted from database (neither from disk)";
            }
        } catch(\PDOException $e) {
            $errors[] = 'Internal server error: '.$e->getMessage();
        }

        return $ok;
    }


	/**
	* Returns a secure name to store in file system, if the generated filename exists returns a non-existing one
	* @param $name original name to be changed-sanitized
	* @param $dir if specified, generated name will be changed if exists in that dir
    * Esto ya lo hace la clase File con get_save_name
    */
    /*
	public static function check_filename($name='',$dir=null){
		$name = preg_replace("/[^a-z0-9~\.]+/","-",strtolower(self::idealiza($name, true)));
		if(is_dir($dir)) {
			while ( file_exists ( "$dir/$name" )) {
				$name = preg_replace ( "/^(.+?)(_?)(\d*)(\.[^.]+)?$/e", "'\$1_'.(\$3+1).'\$4'", $name );
			}
		}
		return $name;
	}
	*/

}

