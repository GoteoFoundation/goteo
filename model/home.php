<?php

namespace Goteo\Model {

    use Goteo\Library\Check,
        Goteo\Library\Text;

    class Home extends \Goteo\Core\Model {

        public
            $item,
            $node,
            $order;

         static public
        $items = array(
             'posts' => 'Entradas de blog',
             'promotes' => 'Proyectos destacados',
             'drops' => 'Capital Riego',
             'feed' => 'Actividad reciente',
             'patrons' => 'Padrinos'
         ),
         $node_items = array(
             'posts' => 'Noticias',
             'promotes' => 'Proyectos',
             'calls' => 'Campañas'
         );


        /*
         *  Devuelve datos de un elemento
         */
        public static function get ($item, $node = \GOTEO_NODE) {
                $query = self::query("
                    SELECT *
                    FROM    home
                    WHERE home.item = :item
                    AND home.node = :node
                    ", array(':item' => $item, ':node'=>$node));
                $home = $query->fetchObject(__CLASS__);

                return $home;
        }

        /*
         * Devuelve elementos en portada
         */
		public static function getAll ($node = \GOTEO_NODE) {
            $array = array();
            $values = array(':node'=>$node);
            $sql = "SELECT
                        home.item as item,
                        home.node as node,
                        home.order as `order`
                    FROM home
                    WHERE home.node = :node
                    ORDER BY `order` ASC
                    ";

            $query = self::query($sql, $values);
            foreach ( $query->fetchAll(\PDO::FETCH_CLASS) as $home) {
                $array[$home->item] = $home;
            }
            return $array;
		}

        public function validate (&$errors = array()) { 
            if (empty($this->item))
                $errors[] = 'Falta elemento';

            if (empty($this->node))
                $errors[] = 'Falta nodo';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'item',
                'node',
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
                $sql = "REPLACE INTO home SET " . $set;
                self::query($sql, $values);

                $extra = array(
                    'node' => $this->node
                );
                Check::reorder($this->item, $this->move, 'home', 'item', 'order', $extra);

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar una pregunta
         */
        public static function delete ($item, $node = \GOTEO_NODE) {
            
            $sql = "DELETE FROM home WHERE item = :item AND node = :node";
            if (self::query($sql, array(':item'=>$item, ':node'=>$node))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que un elemento salga antes  (disminuir el order)
         */
        public static function up ($item, $node = \GOTEO_NODE) {
            $extra = array(
                'node' => $node
            );
            return Check::reorder($item, 'up', 'home', 'item', 'order', $extra);
        }

        /*
         * Para que un elemento salga despues  (aumentar el order)
         */
        public static function down ($item, $node = \GOTEO_NODE) {
            $extra = array(
                'node' => $node
            );
            return Check::reorder($item, 'down', 'home', 'item', 'order', $extra);
        }

        /*
         * Orden para a�adirlo al final
         */
        public static function next ($node = \GOTEO_NODE) {
            $query = self::query('SELECT MAX(`order`) FROM home WHERE node = :node'
                , array(':node'=>$node));
            $order = $query->fetchColumn(0);
            return ++$order;

        }

        /*
         * Elementos disponibles apra portada
         */
		public static function available ($node = \GOTEO_NODE) {
            if ($node == \GOTEO_NODE) {
                $array = self::$items;
            } else {
                $array = self::$node_items;
            }
            $values = array(':node'=>$node);
            $sql = "SELECT
                        home.item as item
                    FROM home
                    WHERE home.node = :node
                    ";

            $query = self::query($sql, $values);
            foreach ( $query->fetchAll(\PDO::FETCH_CLASS) as $used) {
                unset($array[$used->item]);
            }
            return $array;
		}

    }
    
}