<?php

namespace Goteo\Library {

    class Image extends \Goteo\Core\Model {

        public
            $id,
            $name,
            $type,
            $tmp,
            $content,
            $error,
            $size;

        public function __construct ($file) {
            if(is_array($file)) {
                $this->name = $file['name'];
                $this->type = $file['type'];
                $this->tmp = $file['tmp_name'];
                $this->error = $file['error'];
                $this->size = $file['size'];
            }
        }

        public function save(&$errors = array()) {
            if($this->validate($errors)) {
                if(!empty($this->name)) {
                    $data[':name'] = $this->name;
                }

                if(!empty($this->type)) {
                    $data[':type'] = $this->type;
                }

                if(!empty($this->size)) {
                    $data[':size'] = $this->size;
                }

                if(!empty($this->tmp)) {
                    $fp = fopen($this->tmp, "rb");
                    $content = fread($fp, $this->size);
                    $data[':content'] = $content;
                    fclose($fp);
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
                    return $result;
            	} catch(\PDOException $e) {
                    $errors[] = "No se ha podido guardar el archivo en la base de datos: " . $e->getMessage();
                    return false;
    			}
            }
            return false;
		}

		public function validate(&$errors = array()) {
		    if($this->error !== UPLOAD_ERR_OK) {
		        $errors['image'] = $this->error;
		    }

            if(empty($this->name)) {
                $errors['image'] = Text::get('error-image-name');
            }

            if(!empty($this->type)) {
                $allowed_types = array(
    				'image/gif',
    				'image/jpeg',
    				'image/png',
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

            if(!empty($this->size)) {
                $max_upload_size = 2 * 1024 * 1024; // = 2097152 (2 megabytes)
                if($this->size > $max_upload_size) {
                    $errors['image'] = Text::get('error-image-size-too-large');
                }
            }
            else {
                $errors['image'] = Text::get('error-image-size');
            }

            return empty($errors);
		}

	    static public function get ($id) {
            try {
                $query = static::query("
                    SELECT
                        id,
                        name,
                        type,
                        content,
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

		public function getLink ($width = 200, $height = 200) {
		    return '/image/' . $this->id . '/' . $width . '/' . $height;
		}

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

        public function display ($width, $height) {
		    require_once PEAR . 'Image/Transform.php';
            $it =& \Image_Transform::factory('GD');
            if (\PEAR::isError($it)) {
                die($it->getMessage() . '<br />' . $it->getDebugInfo());
            }
            $this->load();
            $src = $this->tmp;
            $it->load($src);
            $it->fit($width,$height);
            return $it->display();
		}

		public function isGIF () {
		    return ($this->type == 'image/gif');
		}

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

        /*
         * Devuelve la imagen en GIF
         */
        static public function gif ($id) {
            $img = static::get($id);
            if ($img->isGIF())
                return $img;
            else
                return $img->toGIF();
        }

        /*
         * Devuelve la imagen en JPG/JPEG
         */
        static public function jpg ($id) {
            $img = static::get($id);
            if ($img->isJPG())
                return $img;
            else
                return $img->toJPG();
        }

        /*
         * Devuelve la imagen en PNG
         */
        static public function png ($id) {
            $img = self::get($id);
            if ($img->isPNG())
                return $img;
            else
                return $img->toPNG();
        }

    	static private function replace_extension($src, $new) {
    	    $pathinfo = pathinfo($src);
    	    unset($pathinfo["basename"]);
    	    unset($pathinfo["extension"]);
    	    return implode(DIRECTORY_SEPARATOR, $pathinfo) . '.' . $new;
    	}

	}

}