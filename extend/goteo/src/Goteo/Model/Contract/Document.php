<?php

namespace Goteo\Model\Contract {

    use Goteo\Library\Check,
        Goteo\Library\FileHandler\File,
        Goteo\Library\Text,
        Goteo\Model;

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
                $errors[] = 'Sin id de proyecto/contrato';
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
                //nombre seguro (quitamos el path!)
                $this->name = basename($this->fp->get_save_name($this->filedir.$this->name));

                $data = array(
                    ':contract' => $this->contract,
                    ':name' => $this->name,
                    ':type' => $this->type,
                    ':size' => $this->size,

                );

                try {

                    //si es un archivo que se sube
                    if (!empty($this->tmp)) {
                        //subimos archivo con permisos privados
                        $uploaded = $this->fp->upload($this->tmp, $this->filedir.$this->name, array('auto_create_dirs' => true, 'perms' => 'bucket-owner-full-control'));

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
                    $query = "INSERT INTO document (id, contract, name, type, size)
                        VALUES ('', :contract, :name, :type, :size)";
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
                $doc = $query->fetchObject(__CLASS__);

                if ($doc instanceof Document) {
                    $doc->filedir = $doc->dir . $doc->contract . '/';
                } else {
                    $doc = false;
                }

                return $doc;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
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
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /*
         * Elimina el registro y el archivo
         * TODO: ROLLBACK
         */
        public function remove (&$errors = array()) {
            $ok = false;
            $this->filedir = $this->contract.'/';

            try {
                $sql = "DELETE FROM document WHERE id = ?";
                $values = array($this->id);
                if (self::query($sql, $values)) {
                     //esborra de disk
                    if ($this->fp->delete($this->filedir . $this->name)) {
                        $ok = true;
                    } else {
                        $errors[] = 'Se ha borrado el registro pero ha fallado al borrar el archivo';
                    }
                } else {
                    $errors[] = 'El sql ha fallado: '.$sql.' con id: '.$this->id;
                }
            } catch(\PDOException $e) {
                $errors[] = 'El sql ha fallado: '.$sql.' con id: '.$this->id;
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

}
