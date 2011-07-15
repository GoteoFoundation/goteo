<?php

namespace Goteo\Model {

    use Goteo\Library\Check;

    class Faq extends \Goteo\Core\Model {

        public
            $id,
            $node,
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
                        node,
                        section,
                        title,
                        description,
                        `order`
                    FROM    faq
                    WHERE id = :id
                    ", array(':id' => $id));
                $faq = $query->fetchObject(__CLASS__);

                return $faq;
        }

        /*
         * Lista de proyectos destacados
         */
        public static function getAll ($section = 'node', $node = \GOTEO_NODE) {

            $query = static::query("
                SELECT
                    id,
                    node,
                    section,
                    title,
                    description,
                    `order`
                FROM    faq
                WHERE section = :section
                AND node = :node
                ORDER BY `order` ASC, title ASC
                ", array(':section' => $section, ':node' => $node));
            
            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }

        public function validate (&$errors = array()) { 
            if (empty($this->node))
                $errors[] = 'Falta nodo';
                //Text::get('mandatory-faq-node');

            if (empty($this->section))
                $errors[] = 'Falta seccion';
                //Text::get('mandatory-faq-section');

            if (empty($this->title))
                $errors[] = 'Falta título';
                //Text::get('mandatory-faq-title');

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'node',
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
                $sql = "REPLACE INTO faq SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                $extra = array(
                    'section' => $this->section,
                    'node' => $this->node
                );
                Check::reorder($this->id, $this->move, 'faq', 'id', 'order', $extra);

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar una pregunta
         */
        public static function delete ($id, $node = \GOTEO_NODE) {
            
            $sql = "DELETE FROM faq WHERE id = :id AND node = :node";
            if (self::query($sql, array(':id'=>$id, ':node'=>$node))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que una pregunta salga antes  (disminuir el order)
         */
        public static function up ($id, $node = \GOTEO_NODE) {
            $query = static::query("SELECT section FROM faq WHERE id = ?", array($id));
            $faq = $query->fetchObject();
            $extra = array(
                'section' => $faq->section,
                'node' => $node
            );
            return Check::reorder($id, 'up', 'faq', 'id', 'order', $extra);
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($id, $node = \GOTEO_NODE) {
            $query = static::query("SELECT section FROM faq WHERE id = ?", array($id));
            $faq = $query->fetchObject();
            $extra = array(
                'section' => $faq->section,
                'node' => $node
            );
            return Check::reorder($id, 'down', 'faq', 'id', 'order', $extra);
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next ($section = 'node', $node = \GOTEO_NODE) {
            $query = self::query('SELECT MAX(`order`) FROM faq WHERE section = :section AND node = :node'
                , array(':section'=>$section, ':node'=>$node));
            $order = $query->fetchColumn(0);
            return ++$order;

        }

        public static function sections () {
            return array(
                'node' => 'Goteo',
                'project' => 'Proyecto',
                'investors' => 'Cofinanciadores',
                'nodes' => 'Nodos'
            );
        }

        public static function colors () {
            return array(
                'node' => '#808285',
                'project' => '#20b3b2',
                'investors' => '#0c4e99',
                'nodes' => '#8f8f8f'
            );
        }


    }
    
}