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

class BaseDocument extends Document {

    public
        $dir = '';
	/**
	 * (non-PHPdoc)
	 * @see Goteo\Core.Model::validate()
	 */
	public function validate(&$errors = array()) {
		if(empty($this->name)) {
            $errors[] = 'Sin nombre de archivo';
        }

        // checkeo de errores de $_FILES
        if($this->error && $this->error !== UPLOAD_ERR_OK) {
            $errors['image'][] = Model\Image::getUploadErrorText($this->error);
            return false;
        }

        if(empty($this->tmp) || $this->tmp == "none") {
            $errors['tmp'] = Text::get('error-image-tmp');
        }

        if(empty($this->size)) {
            $errors['size'] = Text::get('error-image-size');
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
                $query = "INSERT INTO document (name, type, size)
                    VALUES (:name, :type, :size)";
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

    /**
     * Get document data
     * @param varchar(50) $id  Document name
     * @return object instanceof Document or false if it doesn't exist
     */
    public static function getByName ($name) {

        try {
            $sql = "SELECT *
                FROM document
                WHERE name = :name";

            $query = static::query($sql, array(':name' => $name));
            if($doc = $query->fetchObject(__CLASS__)) {
                $doc->filedir = $doc->dir . '/';
                return $doc;
            }

        } catch(\PDOException $e) {
            throw new ModelNotFoundException($e->getMessage());
        }
		throw new ModelNotFoundException('Document not found!');
	}


}

