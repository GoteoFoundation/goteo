<?php

namespace Goteo\Model\Contract {

    use Goteo\Library\Check,
        Goteo\Library\File,
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
            $filedir;
        private $fp;

        // ruta en data a contract_docs (Hay alli un .htaccess para prohibir el acceso publico si es local)
        public static $dir = 'contracts/';

        /**
         * Constructor.
         *
         * @param type array	$file	Array $_FILES.
         */
        public function setFile ($file) {

            $this->filedir = self::$dir . $this->contract . '/';

            if(is_array($file) && !empty($file['name'])) {
                $this->name = $file['name'];
                $this->type = $file['type'];
                $this->tmp = $file['tmp_name'];
                $this->error = $file['error'];
                $this->size = $file['size'];

                $this->fp = new File();
                return true;
            } else {
                return false;
            }
        }
        /**
         * retorna nombre "seguro", que no existe ya
         */
        public function save_name() {
            //un nombre que no exista
            $name = $this->fp->get_save_name(self::$dir . $this->name);
            if(self::$dir) $name = substr($name, strlen(self::$dir));
            return $name;
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
			if(is_uploaded_file($this->tmp)) {
				if($this->error !== UPLOAD_ERR_OK) {
					$errors[] = $this->error;
				}

				if(empty($this->tmp) || $this->tmp == "none") {
					$errors[] = Text::get('error-image-tmp');
				}

				if(!empty($this->size)) {
					$max_upload_size = 2 * 1024 * 1024; // = 2097152 (2 megabytes)
					if($this->size > $max_upload_size) {
						$errors[] = Text::get('error-image-size-too-large');
					}
				}
				else {
					$errors[] = Text::get('error-image-size');
				}
			}
            return empty($errors);
		}

        /**
         * Solo graba, no actualiza
         * (non-PHPdoc)
         * @see Goteo\Core.Model::save()
         */
        public function save(&$errors = array()) {

            try {

                if($this->validate($errors)) {
                    //nombre seguro
                    $name = $this->save_name();

                    $data = array(
                        ':contract' => $this->contract,
                        ':name' => $name,
                        ':type' => $this->type,
                        ':size' => $this->size,

                    );

                    //si es un archivo que se sube
                    if(!empty($this->tmp)) {

                        $destino = $this->filedir . $name;

                        //subir el archivo
                        if(!$this->fp->upload($this->tmp, $destino, 'bucket-owner-full-control')) {
                            $errors[] = $this->tmp . ' no se ha podido ubicar en '.$destino;
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
                        $this->name = $name;
                        return true;
                    } else {
                        $errors[] = "Fallo sql: $query " . print_r($data, true);
                        return false;
                    }
                }
                
                return false;
                
            } catch(\PDOException $e) {
                $errors[] = "No se ha podido guardar el archivo en la base de datos: " . $e->getMessage();
                return false;
            }
		}
        
        /**
         * Get documentdata
         * @param varcahr(50) $id  Document identifier
         * @return object instanceof stdClass
         */
	 	public static function get ($id) {
            
            try {
                $sql = "SELECT * 
                    FROM document 
                    WHERE id = :id";
                
                $query = static::query($sql, array(':id' => $id));
                $doc = $query->fetchObject(__CLASS__);
                $doc->filedir = self::$dir . $doc->contract . '/';
                
                return $doc;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /**
         * Get the documents for a contract
         * @param varcahr(50) $id  Contract identifier
         * @return array of documents
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
                    $document->filedir = self::$dir . $document->contract . '/';
                    $array[] = $document;
                }
                
                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /*
         * elimina el registro y el archivo
         */
        public function remove (&$errors = array()) {

            try {
                if(!($this->fp instanceof File)) $this->fp = new File();
                // print_r($this->filedir . $this->name);die;
                $sql = "DELETE FROM document WHERE id = ?";
                if (self::query($sql, array($this->id))) {
                     //esborra de disk
                    if ($this->fp->delete($this->filedir . $this->name)) {
                        return true;
                    } else {
                        $errors[] = 'Se ha borrado el registro pero el unlink() ha fallado';
                        return false;
                    }
                } else {
                    $errors[] = 'El sql ha fallado: '.$sql.' con id: '.$this->id;
                    return false;
                }
            } catch(\PDOException $e) {
                return false;
            }

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
