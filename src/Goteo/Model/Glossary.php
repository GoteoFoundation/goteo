<?php

namespace Goteo\Model {

    use \Goteo\Model\Project\Media,
        \Goteo\Model\Image,
        \Goteo\Library\Text,
        \Goteo\Library\Message;

    class Glossary extends \Goteo\Core\Model {

        public
            $id,
            $title,
            $text,
            $image,
            $media,
            $legend,
            $gallery = array(); // array de instancias image de glossary_image

        /*
         *  Devuelve datos de una entrada
         */
        public static function get ($id) {

                //Obtenemos el idioma de soporte
                $lang=self::default_lang_by_id($id, 'glossary_lang', \LANG);

                $query = static::query("
                    SELECT
                        glossary.id as id,
                        IFNULL(glossary_lang.title, glossary.title) as title,
                        IFNULL(glossary_lang.text, glossary.text) as text,
                        IFNULL(glossary_lang.legend, glossary.legend) as legend,
                        glossary.media as `media`,
                        glossary.image as `image`,
                        glossary.gallery as `gallery`
                    FROM    glossary
                    LEFT JOIN glossary_lang
                        ON  glossary_lang.id = glossary.id
                        AND glossary_lang.lang = :lang
                    WHERE glossary.id = :id
                    ", array(':id' => $id, ':lang'=>$lang));

                if($glossary = $query->fetchObject(__CLASS__)) {

                    // video
                    if (isset($glossary->media)) {
                        $glossary->media = new Media($glossary->media);
                    }

                    // campo calculado gallery
                    if (!empty($glossary->gallery) && $glossary->gallery !== 'empty') {
                        $glossary->gallery = Image::getGallery($glossary->gallery);
                    } elseif ($glossary->gallery !== 'empty') {
                        $glossary->setGallery();
                    } else {
                        $glossary->gallery = array();
                    }

                    if (!empty($glossary->image) && $glossary->image !== 'empty') {
                        $glossary->image = Image::get($glossary->image);
                    } elseif ($glossary->image !== 'empty') {
                        $glossary->setImage();
                    } else {
                        $glossary->image = null;
                    }
                }
                return $glossary;
        }

        /*
         * Lista de entradas por orden alfabético
         */
        public static function getAll () {

            $list = array();

            if(self::default_lang(\LANG)=='es') {
                $different_select=" IFNULL(glossary_lang.title, glossary.title) as title,
                                    IFNULL(glossary_lang.text, glossary.text) as `text`,
                                    IFNULL(glossary_lang.legend, glossary.legend) as `legend`";
                }
            else {
                    $different_select=" IFNULL(glossary_lang.title, IFNULL(eng.title, glossary.title)) as title,
                                        IFNULL(glossary_lang.text, IFNULL(eng.text, glossary.text)) as `text`,
                                        IFNULL(glossary_lang.legend, IFNULL(eng.legend, glossary.legend)) as `legend`";
                    $eng_join=" LEFT JOIN glossary_lang as eng
                                    ON  eng.id = glossary.id
                                    AND eng.lang = 'en'";
                }

            $sql="
                SELECT
                    glossary.id as id,
                    $different_select,
                    glossary.media as `media`,
                    glossary.image as `image`,
                    glossary.gallery as `gallery`
                FROM    glossary
                LEFT JOIN glossary_lang
                    ON  glossary_lang.id = glossary.id
                    AND glossary_lang.lang = :lang
                $eng_join
                        ";

            $sql .= " ORDER BY title ASC
                ";

            $query = static::query($sql, array(':lang'=>\LANG));

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $glossary) {

                // video
                if (!empty($glossary->media)) {
                    $glossary->media = new Media($glossary->media);
                }

                // campo calculado gallery
                if (!empty($glossary->gallery) && $glossary->gallery !== 'empty') {
                    $glossary->gallery = Image::getGallery($glossary->gallery);
                } elseif ($glossary->gallery !== 'empty') {
                    $glossary->setGallery();
                } else {
                    $glossary->gallery = array();
                }

                if (!empty($glossary->image) && $glossary->image !== 'empty') {
                    $glossary->image = Image::get($glossary->image);
                } elseif ($glossary->image !== 'empty') {
                    $glossary->setImage();
                } else {
                    $glossary->image = null;
                }

                $list[$glossary->id] = $glossary;
            }

            return $list;
        }

        public function validate (&$errors = array()) {
            if (empty($this->title))
                $errors['title'] = 'Falta título';

            if (empty($this->text))
                $errors['text'] = 'Falta texto';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'title',
                'text',
                'legend',
                'media'
                );

            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO glossary SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                // Luego la imagen
                if (!empty($this->id) && is_array($this->image) && !empty($this->image['name'])) {
                    $image = new Image($this->image);

                    if ($image->save($errors)) {

                        /**
                         * Guarda la relación NM en la tabla 'glossary_image'.
                         */
                        if(!empty($image->id)) {
                            self::query("REPLACE glossary_image (glossary, image) VALUES (:glossary, :image)", array(':glossary' => $this->id, ':image' => $image->id));
                        }

                        // Actualiza el campo calculado
                        $this->setGallery();
                        $this->setImage();

                    } else {
                        Message::Error(Text::get('image-upload-fail') . implode(', ', $errors));
                    }
                }

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar una entrada
         */
        public function delete ($id = null) {
            if(empty($id) && $this->id) {
                $id = $this->id;
            }
            if(empty($id)) {
                // throw new Exception("Delete error: ID not defined!");
                return false;
            }

            $sql = "DELETE FROM glossary WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {

                // que elimine tambien sus imágenes
                $sql = "DELETE FROM glossary_image WHERE glossary = :id";
                self::query($sql, array(':id'=>$id));

                return true;
            }
            return false;
        }

        /*
         * Recalcular galeria
         */
        public function setGallery () {
            $this->gallery = Image::setGallery('glossary', $this->id);
            return true;
        }

        /*
         * Recalcular imagen principal
         */
        public function setImage () {
            $this->image = Image::setImage('glossary', $this->id, $this->gallery);
            return true;
        }

    }

}
