<?php

namespace Goteo\Model\Project {

    use Goteo\Library\Check,
        Goteo\Library\Text,
        Goteo\Model;

    class Image extends \Goteo\Core\Model {

        public
            $id,
            $image,
            $section,
            $order;

        function validate(&$errors = array()) {
            asort($errors);
            return true;
        }
        
        function save(&$errors = array()) {
            asort($errors);
           return null;
        }
        
        /**
         * Get the images for a project
         *
         * Se usa en la gestión de imágenes de proyecto en admin/project/images
         *
         * @param varcahr(50) $id  Project identifier
         * @return array of categories identifiers
         */
	 	public static function get ($id, $section = null) {
            
            $URL = \SITE_URL;

            
            $array = array ();
            try {
                $values = array(':id' => $id);
                
                if (!empty($section)) {
                    $sqlFilter = " AND section = :section";
                    $values[':section'] = $section;
                } elseif (isset($section) && $section == '') {
                    $sqlFilter = " AND (section = '' OR section IS NULL)";
                }
                
                $sql = "SELECT * 
                    FROM project_image 
                    WHERE project = :id 
                    $sqlFilter
                    ORDER BY `order` ASC, image DESC";

                $query = static::query($sql, $values);
                $images = $query->fetchAll(\PDO::FETCH_OBJ);
                foreach ($images as $image) {
                    $image->imageData = Model\Image::get($image->image);
                    if (!empty($image->url)) {
                        $image->link = $image->url;
                    }
                    
                    $array[] = $image;
                }
                
                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /**
         * Get list of image names
         *
         * Se usa para guardar serializado en el campo gallery
         *
         * @param varcahr(50) $id  Project identifier
         * @return array of images and urls
         */
	 	public static function getList ($id, $section = null) {

            $array = array ();
            try {
                $values = array(':id' => $id);

                if (!empty($section)) {
                    $sqlFilter = " AND section = :section";
                    $values[':section'] = $section;
                } else {
                    $sqlFilter = " AND (section = '' OR section IS NULL)";
                }

                $sql = "SELECT image, url
                    FROM project_image
                    WHERE project = :id
                    $sqlFilter
                    ORDER BY `order` ASC";

                $query = static::query($sql, $values);
                $images = $query->fetchAll(\PDO::FETCH_OBJ);
                foreach ($images as $image) {
                    if (!empty($image->url)) {
                        $image->link = $image->url;
                    } else {
                        $image->link = '';
                    }

                    $array[] = array('img'=>$image->image, 'url'=>$image->link);
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /*
         * Solo imágenes para galeria
         */
        public static function setGallery ($id) {

            $galleries = array();

            // galerías de sección
            foreach (static::sections() as $sec => $val) {
                // sacar galeria de glossary_image
                // no puede ser de Model\Image porque estas imagenes de seccion llevan enlace
                $galleries[$sec] = self::getList($id, $sec);
            }

            $sGallery = serialize($galleries);
            if (strlen($sGallery) > 10000) {
                // tenemos un problema, hay que aumentar el campo
                @mail(\GOTEO_FAIL_MAIL,
                    'Galeria de proyecto serializada no cabe. ',
                    'Galeria de proyecto serializada no cabe. '.SITE_URL.' '. \trace($sGallery));

            }

            // guardar serializado en la tabla proyecto
            $sql = "UPDATE project SET gallery = :gallery WHERE id = :id";
            self::query($sql, array(':gallery'=>$sGallery, ':id'=>$id));

            return $galleries;
        }

        /*
         * Recalcular imagen principal
         */
        public function setImage ($id, $gallery) {

            // sacar objeto imagen de la galeria
            $image = $gallery[0]->imageData;

            // guardar en la base de datos
            $sql = "UPDATE project SET image = :image WHERE id = :id";
            self::query($sql, array(':image'=>$image->id, ':id'=>$id));

            return $image;

        }


        /*
         * Para aplicar una seccion o un enlace
         */
        public static function update ($project, $image, $field, $value) {
            
            $sql = "UPDATE project_image SET `$field` = :val WHERE project = :project AND MD5(image) = :image";
            if (self::query($sql, array(':project'=>$project, ':image'=>$image, ':val'=>$value))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que una imagen salga antes  (disminuir el order)
         */
        public static function up ($project, $image, $section) {
            return Check::reorder($image, 'up', 'project_image', 'image', 'order', array('project' => $project, 'section' => $section));
        }

        /*
         * Para que una imagen salga despues  (aumentar el order)
         */
        public static function down ($project, $image, $section) {
            return Check::reorder($image, 'down', 'project_image', 'image', 'order', array('project' => $project, 'section' => $section));
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next ($project) {
            $query = self::query('SELECT MAX(`order`) FROM project_image WHERE project = :project'
                , array(':project'=>$project));
            $order = $query->fetchColumn(0);
            return ++$order;

        }

        public static function sections () {
            return array(
                '' => 'Galería',
                'about' => Text::get('overview-field-about'),
                'motivation' => Text::get('overview-field-motivation'),
                'goal' => Text::get('overview-field-goal'),
                'related' => Text::get('overview-field-related'),
                'reward' => Text::get('overview-field-reward'),
                'play-video' => Text::get('overview-field-play-video')
            ); 
       }





        // quizás no usamos esto para proyecto....

        /*
         * Recalcular galeria
         * Para proyecto hay secciones y orden
         *
        public function setGallery () {
            // $section
            $this->gallery[] = Image::setGallery('project', $this->id);
            return true;
        }

        /*
         * Recalcular imagen principal
         * Para widget es la primera de la galería principal
         *
        public function setImage () {
            $this->image = Image::setImage('project', $this->id, $this->gallery);
            return true;
        }

*/

    }
    
}