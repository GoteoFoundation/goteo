<?php

namespace Goteo\Model {

    use \Goteo\Model\Project\Media,
        \Goteo\Model\Image,
        \Goteo\Library\Text,
        \Goteo\Library,
        \Goteo\Library\Check,
        \Goteo\Application;
    use Goteo\Application\Lang;
    use Goteo\Application\Config;

    class Info extends \Goteo\Core\Model {

        public
            $id,
            $title,
            $text,
            $image,
            $media,
            $legend,
            $publish,
            $order,
            $share_facebook,
            $share_twitter,
            $gallery = array(); // array de instancias image de info_image

        /*
         *  Devuelve datos de una entrada
         */
        public static function get ($id, $lang = null) {

                //Obtenemos el idioma de soporte
                $lang=self::default_lang_by_id($id, 'info_lang', $lang);

                $query = static::query("
                    SELECT
                        info.id as id,
                        info.node as node,
                        IFNULL(info_lang.title, info.title) as title,
                        IFNULL(info_lang.text, info.text) as text,
                        IFNULL(info_lang.legend, info.legend) as legend,
                        IFNULL(info_lang.share_facebook, info.share_facebook) as share_facebook,
                        IFNULL(info_lang.share_twitter, info.share_twitter) as share_twitter,
                        info.media as `media`,
                        info.image as `image`,
                        info.publish as `publish`,
                        info.order as `order`
                    FROM    info
                    LEFT JOIN info_lang
                        ON  info_lang.id = info.id
                        AND info_lang.lang = :lang
                    WHERE info.id = :id
                    ", array(':id' => $id, ':lang'=>$lang));

                if($info = $query->fetchObject(__CLASS__)) {

                    // video
                    if (isset($info->media)) {
                        $info->media = new Media($info->media);
                    }

                    $info->gallery = Image::getModelGallery('info', $info->id);
                    $info->image = Image::getModelImage($info->image, $info->gallery);
                }
                return $info;
        }

        /*
         * Lista de entradas por orden alfabético
         */
        public static function getAll ($published = false, $node = \GOTEO_NODE) {
            $lang = Lang::current();
            $list = array();

            if(self::default_lang($lang) === Config::get('lang')) {
                $different_select=" IFNULL(info_lang.title, info.title) as title,
                                    IFNULL(info_lang.text, info.text) as `text`,
                                    IFNULL(info_lang.legend, info.legend) as `legend`,
                                    IFNULL(info_lang.share_facebook, info.share_facebook) as `share_facebook`,
                                    IFNULL(info_lang.share_twitter, info.share_twitter) as `share_twitter`";
                }
            else {
                    $different_select=" IFNULL(info_lang.title, IFNULL(eng.title, info.title)) as title,
                                        IFNULL(info_lang.text, IFNULL(eng.text, info.text)) as `text`,
                                        IFNULL(info_lang.legend, IFNULL(eng.legend, info.legend)) as `legend`,
                                        IFNULL(info_lang.share_facebook, IFNULL(eng.share_facebook, info.share_facebook)) as `share_facebook`,
                                        IFNULL(info_lang.share_twitter, IFNULL(eng.share_twitter, info.share_twitter)) as `share_twitter`";
                    $eng_join=" LEFT JOIN info_lang as eng
                                    ON  eng.id = info.id
                                    AND eng.lang = 'en'";
                }

                $sql="
                    SELECT
                        info.id as id,
                        $different_select,
                        info.media as `media`,
                        info.image as `image`,
                        info.publish as `publish`,
                        info.order as `order`
                    FROM    info
                    LEFT JOIN info_lang
                        ON  info_lang.id = info.id
                        AND info_lang.lang = :lang
                    $eng_join
                    WHERE info.node = :node
                    ";

            if ($published == true) {
                $sql .= " AND info.publish = 1";
            }

            $sql .= " ORDER BY `order` ASC
                ";

            $query = static::query($sql, array(':node'=>$node, ':lang'=>$lang));

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $info) {

                // video
                if (!empty($info->media)) {
                    $info->media = new Media($info->media);
                }

                $info->gallery = Image::getModelGallery('info', $info->id);
                $info->image = Image::getModelImage($info->image, $info->gallery);



                $list[$info->id] = $info;
            }

            return $list;
        }

        public function validate (&$errors = array()) {
            if (empty($this->title))
                $errors['title'] = 'Falta título';

            if (empty($this->text))
                $errors['text'] = 'Falta texto';

            if (empty($this->node))
                $this->node = \GOTEO_NODE;

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'node',
                'title',
                'text',
                'media',
                'legend',
                'order',
                'publish',
                'share_facebook',
                'share_twitter'
                );

            try {
                //automatic $this->id assignation
                $this->dbInsertUpdate($fields);

                // Luego la imagen
                if (!empty($this->id) && is_array($this->image) && !empty($this->image['name'])) {
                    $image = new Image($this->image);
                    if ($image->addToModelGallery('info', $this->id)) {
                        $this->gallery[] = $image;
                        // Pre-calculated field
                        $this->gallery[0]->setModelImage('info', $this->id);
                    }
                    else {
                       Application\Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                    }
                }

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }


        /*
         * Para que una entrada salga antes  (disminuir el order)
         */
        public static function up ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'up', 'info', 'id', 'order', $extra);
        }

        /*
         * Para que una entrada salga despues  (aumentar el order)
         */
        public static function down ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'down', 'info', 'id', 'order', $extra);
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next ($node = \GOTEO_NODE) {
            $query = self::query('SELECT MAX(`order`) FROM info WHERE node = :node'
                , array(':node'=>$node));
            $order = $query->fetchColumn(0);
            return ++$order;

        }

    }

}
