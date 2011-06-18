<?php

namespace Goteo\Model {

    use Goteo\Library\Check;

    class News extends \Goteo\Core\Model {

        public
            $id,
            $title,
            $url,
            $order;

        /*
         *  Devuelve datos de un destacado
         */
        public static function get ($id) {
                $sql = static::query("
                    SELECT
                        id,
                        title,
                        url,
                        `order`
                    FROM    news
                    WHERE id = :id
                    ", array(':id' => $id));
                $news = $sql->fetchObject(__CLASS__);

                return $news;
        }

        /*
         * Lista de proyectos destacados
         */
        public static function getAll ($section = 'node') {

            $sql = static::query("
                SELECT
                    id,
                    title,
                    url,
                    `order`
                FROM    news
                ORDER BY `order` ASC, title ASC
                ");
            
            return $sql->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }

        public function validate (&$errors = array()) { 
            if (empty($this->title))
                $errors[] = 'Falta título';
                //Text::get('mandatory-news-title');

            if (empty($this->url))
                $errors[] = 'Falta url';
                //Text::get('mandatory-news-url');

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
                'url',
                'order'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO news SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                Check::reorder($this->id, 'up', 'news');

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar una pregunta
         */
        public static function delete ($id) {
            
            $sql = "DELETE FROM news WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que una pregunta salga antes  (disminuir el order)
         */
        public static function up ($id) {
            return Check::reorder($id, 'up', 'news');
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($id) {
            return Check::reorder($id, 'down', 'news');
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next () {
            $sql = self::query('SELECT MAX(`order`) FROM news');
            $order = $sql->fetchColumn(0);
            return ++$order;

        }

    }
    
}