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
         * @param varcahr(50) $id  Project identifier
         * @return array of categories identifiers
         */
	 	public static function get ($id, $section = null) {
            
            $URL = (NODE_ID != GOTEO_NODE) ? NODE_URL : SITE_URL;
            
            
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
                    ORDER BY `order`";
                
                $query = static::query($sql, $values);
                $images = $query->fetchAll(\PDO::FETCH_OBJ);
                foreach ($images as $image) {
                    $image->imageData = Model\Image::get($image->image);
                    if (!empty($image->url)) {
                        $image->link = (substr($image->url, 0, strlen('http://')) == 'http://') ? $image->url : $URL.'/'.$image->url;
                    }
                    
                    $array[] = $image;
                }
                
                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /*
         * la primera para el widget
         */
        public static function getFirst ($id) {

            try {
                $sql = "SELECT image FROM project_image WHERE project = ? AND (section = '' OR section IS NULL) ORDER BY `order` ASC LIMIT 1";
                $query = self::query($sql, array($id));
                $first = $query->fetchColumn(0);
                return Model\Image::get($first);
                
            } catch(\PDOException $e) {
                return false;
            }

        }
        
        /*
         * Solo imágenes para galeria
         */
        public static function getGallery ($id) {

            $gallery = array();

            try {
                $sql = "SELECT image FROM project_image WHERE project = ? AND (section = '' OR section IS NULL) ORDER BY `order` ASC";
                $query = self::query($sql, array($id));
                foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $image) {
                    $gallery[] = Model\Image::get($image['image']);
                }

                return $gallery;
            } catch(\PDOException $e) {
                return false;
            }

        }
        
        
        /*
         * Para aplicar una seccion o un enlace
         */
        public static function update ($project, $image, $field, $value) {
            
            $sql = "UPDATE project_image SET `$field` = :val WHERE project = :project AND image = :image";
            if (self::query($sql, array(':project'=>$project, ':image'=>$image, ':val'=>$value))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que una imagen salga antes  (disminuir el order)
         */
        public static function up ($project, $image) {
            return Check::reorder($image, 'up', 'project_image', 'image', 'order', array('project' => $project));
        }

        /*
         * Para que una imagen salga despues  (aumentar el order)
         */
        public static function down ($project, $image) {
            return Check::reorder($image, 'down', 'project_image', 'image', 'order', array('project' => $project));
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
                'reward' => Text::get('overview-field-reward')
            ); 
       }

    }
    
}