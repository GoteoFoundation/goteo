<?php

namespace Goteo\Model {

    use Goteo\Library\Check,
        Goteo\Library\Text;

    class Criteria extends \Goteo\Core\Model {

        public
            $id,
            $section,
            $title,
            $description,
            $order;

        /*
         *  Devuelve datos de un destacado
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT
                        id,
                        section,
                        title,
                        description,
                        `order`
                    FROM    criteria
                    WHERE id = :id
                    ", array(':id' => $id));
                $criteria = $query->fetchObject(__CLASS__);

                return $criteria;
        }

        /*
         * Lista de proyectos destacados
         */
        public static function getAll ($section = 'project') {

            $query = static::query("
                SELECT
                    id,
                    section,
                    title,
                    description,
                    `order`
                FROM    criteria
                WHERE section = :section
                ORDER BY `order` ASC, title ASC
                ", array(':section' => $section));
            
            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }

        public function validate (&$errors = array()) { 
            if (empty($this->section))
                $errors[] = 'Falta seccion';
                //Text::get('mandatory-criteria-section');

            if (empty($this->title))
                $errors[] = 'Falta título';
                //Text::get('mandatory-criteria-title');

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'section',
                'title',
                'description',
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
                $sql = "REPLACE INTO criteria SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                $extra = array(
                    'section' => $this->section
                );
                Check::reorder($this->id, $this->move, 'criteria', 'id', 'order', $extra);

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
            
            $sql = "DELETE FROM criteria WHERE id = :id";
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
            $query = static::query("SELECT section FROM criteria WHERE id = ?", array($id));
            $criteria = $query->fetchObject();
            $extra = array(
                'section' => $criteria->section
            );
            return Check::reorder($id, 'up', 'criteria', 'id', 'order', $extra);
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($id) {
            $query = static::query("SELECT section FROM criteria WHERE id = ?", array($id));
            $criteria = $query->fetchObject();
            $extra = array(
                'section' => $criteria->section
            );
            return Check::reorder($id, 'down', 'criteria', 'id', 'order', $extra);
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next ($section = 'project') {
            $query = self::query('SELECT MAX(`order`) FROM criteria WHERE section = :section'
                , array(':section'=>$section));
            $order = $query->fetchColumn(0);
            return ++$order;

        }

        public static function sections () {
            return array(
                'project' => Text::get('criteria-project-section-header'),
                'owner' => Text::get('criteria-owner-section-header'),
                'reward' => Text::get('criteria-reward-section-header')
            );
        }


    }
    
}