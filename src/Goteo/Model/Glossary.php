<?php

namespace Goteo\Model {

    use \Goteo\Model\Project\Media,
        \Goteo\Model\Image,
        \Goteo\Library\Text,
        \Goteo\Application\Message;

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
                        glossary.image as `image`
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

                    $glossary->gallery = Image::getModelGallery('glossary', $glossary->id);
                    $glossary->image = Image::getModelImage($glossary->image, $glossary->gallery);
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
                    glossary.image as `image`
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

                $glossary->gallery = Image::getModelGallery('glossary', $glossary->id);
                $glossary->image = Image::getModelImage($glossary->image, $glossary->gallery);

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
                // 'id',
                'title',
                'text',
                'legend',
                'media'
                );

            try {
                //automatic $this->id assignation
                $this->insertUpdate($fields);

                // Luego la imagen
                if (!empty($this->id) && is_array($this->image) && !empty($this->image['name'])) {
                    $image = new Image($this->image);

                    if ($image->addToModelGallery('glossary', $this->id)) {
                        $this->gallery[] = $image;
                        // Pre-calculated field
                        $this->gallery[0]->setModelImage('glossary', $this->id);
                    }
                    else {
                        Message::Error(Text::get('image-upload-fail') . implode(', ', $errors));
                    }
                }

                return true;
            } catch(\PDOException $e) {
                $errors[] = "Save error: " . $e->getMessage();
                return false;
            }
        }

        /**
         * Static compatible version of parent delete()
         * @param  [type] $id [description]
         * @return [type]     [description]
         */
        public function delete($id = null) {
            if(empty($id)) return parent::delete();

            if(!($ob = Glossary::get($id))) return false;
            return $ob->delete();

        }


    }

}
