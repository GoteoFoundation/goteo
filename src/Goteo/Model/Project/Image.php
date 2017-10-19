<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

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

        //TODO: use URL base from config
        private static function get_image_resource_url($url) {
            if($url) {
                if(stripos($url, 'http') === 0 && strpos($url, '://') !== false) {
                    return $url;
                }
                if(stripos($url, 'goteo.org') === 0) {
                    return 'http://' . $url;
                }
                if(stripos($url, '/') !== 0) {
                    $url = '/' . $url;
                }
                return 'http://goteo.org' . $url;
            return '';
            }
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
                $images = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);

                foreach ($images as $image) {
                    $image->imageData = Model\Image::get($image->image);
                    $image->link = self::get_image_resource_url($image->url);
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
                    $array[] = (object) array( 'imageData' => Model\Image::get($image->image),
                                      'link' => self::get_image_resource_url($image->url));
                }
                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /*
         * Same as getList by with several galleries at once
         */
        public static function getGalleries ($id, $section = null) {
            $galleries = array();

            // galerías de sección
            foreach (static::sections() as $sec => $val) {
                // sacar galeria de glossary_image
                // no puede ser de Model\Image porque estas imagenes de seccion llevan enlace
                if(!is_null($section)) {
                    if(is_array($section) && !in_array($sec, $section)) continue;
                    elseif($sec === $section) continue;
                }

                $galleries[$sec] = self::getList($id, $sec);
            }

            return $galleries;
        }

        /*
         * Recalcular imagen principal
         */
        public static function setImage ($id, $gallery) {
            if($gallery instanceOf Model\Image) {
                $image = $gallery;
            } else {
                // sacar objeto imagen de la galeria
                $image = $gallery[0]->imageData;
            }
            if(!$image instanceOf Model\Image) {
                return new Model\Image();
            }

            // guardar en la base de datos
            $sql = "UPDATE project SET image = :image WHERE id = :id";
            self::query($sql, array(':image'=>$image->id, ':id'=>$id));

            return $image;

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
                'play-video' => Text::get('overview-field-play-video'),
                '' => Text::get('overview-field-main-gallery'),
                'about' => Text::get('overview-field-about'),
                'motivation' => Text::get('overview-field-motivation'),
                'goal' => Text::get('overview-field-goal'),
                'related' => Text::get('overview-field-related'),
                // 'reward' => Text::get('overview-field-reward')
            );
       }

        // Helpers
        public function getLink() {
            $args = func_get_args();
            return call_user_func_array(array($this->imageData, 'getLink'), $args);
        }
        public function getName() {
            return $this->imageData->name;
        }
    }

}
