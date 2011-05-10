<?php

namespace Goteo\Model {
    
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
        public static function getAll ($section = 'node', $node = 'goteo') {

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

            if (empty($this->section))
                $errors[] = 'Falta seccion';

            if (empty($this->title))
                $errors[] = 'Falta título';

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

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar una pregunta
         */
        public static function delete ($id, $node = 'goteo') {
            
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
        public static function up ($id, $node = 'goteo') {

            $query = self::query('SELECT `order` FROM faq WHERE id = :id AND node = :node'
                , array(':id'=>$id, ':node'=>$node));
            $order = $query->fetchColumn(0);

            $order--;
            if ($order < 1)
                $order = 1;

            $sql = "UPDATE faq SET `order`=:order WHERE id = :id AND node = :node";
            if (self::query($sql, array(':order'=>$order, ':id'=>$id, ':node'=>$node))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($section, $node = 'goteo') {

            $query = self::query('SELECT `order` FROM faq WHERE id = :id AND node = :node'
                , array(':id'=>$id, ':node'=>$node));
            $order = $query->fetchColumn(0);

            $order++;

            $sql = "UPDATE faq SET `order`=:order WHERE id = :id AND node = :node";
            if (self::query($sql, array(':order'=>$order, ':id'=>$id, ':node'=>$node))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Orden para añadirlo al final
         */
        public static function next ($section = 'node', $node = 'goteo') {
            $query = self::query('SELECT MAX(`order`) FROM faq WHERE section = :section AND node = :node'
                , array(':section'=>$section, ':node'=>$node));
            $order = $query->fetchColumn(0);
            return ++$order;

        }

        public static function sections () {
            return array(
                'node' => 'Goteo',
                'project' => 'Proyecto',
                'investors' => 'Cofinanciadores'
            );
        }


    }
    
}