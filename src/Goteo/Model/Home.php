<?php

namespace Goteo\Model {

    use Goteo\Library\Check,
        Goteo\Library\Text;

    class Home extends \Goteo\Core\Model {

        public
            $item,
            $type,
            $node,
            $order;

         static public
         $types = array(
             'side' => 'Laterales',
             'main' => 'Centrales'
         ),
        $items = array(
             'posts' => 'Entradas de blog',
             'promotes' => 'Proyectos destacados',
             'drops' => 'Capital Riego',
             'feed' => 'Actividad reciente',
             'patrons' => 'Padrinos',
             'stories' => 'Historias exitosas',
             'news' => 'Banner de prensa'
         ),
         $node_items = array(
             'posts' => 'Novedades',
             'promotes' => 'Proyectos',
             'calls' => 'Convocatorias',
             'patrons' => 'Padrinos',
             'stories' => 'Historias exitosas'
         ),
         $node_side_items = array(
             'searcher' => 'Selector proyectos',
             'categories' => 'Categorias de proyectos',
             'summary' => 'Resumen proyectos',
             'sumcalls' => 'Resumen convocatorias',
             'sponsors' => 'Patrocinadores'
         ),
         $admins = array(
             'promotes' => '/admin/promote',
             'drops' => '/admin/calls',
             'calls' => '/admin/campaigns',
             'posts' => '/admin/blog',
             'patrons' => '/admin/patron',
             'sponsors' => '/admin/sponsors',
             'stories' => '/admin/stories',
             'news' => '/admin/news'
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
                    AND (type = 'main' OR type IS NULL)
                    ORDER BY `order` ASC
                    ";

            $query = self::query($sql, $values);
            foreach ( $query->fetchAll(\PDO::FETCH_CLASS) as $home) {
                $array[$home->item] = $home;
            }
            return $array;
		}

        /*
         * Devuelve elementos laterales de portada para nodos
         */
		public static function getAllSide ($node = \GOTEO_NODE) {
            $array = array();
            $values = array(':node'=>$node);
            $sql = "SELECT
                        home.item as item,
                        home.node as node,
                        home.order as `order`
                    FROM home
                    WHERE home.node = :node
                    AND type = 'side'
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

            if (empty($this->type))
                $this->type = 'main';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'item',
                'type',
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
                    'node' => $this->node,
                    'type' => $this->type
                );
                Check::reorder($this->item, $this->move, 'home', 'item', 'order', $extra);

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar un elemento
         */
        public function delete ($item = null, $node = null, $type = null) {
            if(empty($item) && $this->item) $item = $this->item;
            if(empty($node) && $this->node) $node = $this->node;
            if(empty($type) && $this->type) $type = $this->node;
            if(empty($node)) $node = \GOTEO_NODE;
            if(empty($type)) $type = 'main';
            if(empty($item)) return false;

            $sql = "DELETE FROM home WHERE item = :item AND node = :node AND type = :type";
            try {
                self::query($sql, array(':item'=>$item, ':node'=>$node, ':type'=>$type));
            } catch (\PDOException $e) {
                // throw new Exception("Delete error in $sql");
                return false;
            }
            return true;
        }

        /*
         * Para que un elemento salga antes  (disminuir el order)
         */
        public static function up ($item, $node = \GOTEO_NODE, $type = 'main') {
            $extra = array(
                'node' => $node,
                'type' => $type
            );
            return Check::reorder($item, 'up', 'home', 'item', 'order', $extra);
        }

        /*
         * Para que un elemento salga despues  (aumentar el order)
         */
        public static function down ($item, $node = \GOTEO_NODE, $type = 'main') {
            $extra = array(
                'node' => $node,
                'type' => $type
            );
            return Check::reorder($item, 'down', 'home', 'item', 'order', $extra);
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next ($node = \GOTEO_NODE, $type = 'main') {
            $query = self::query('SELECT MAX(`order`) FROM home WHERE node = :node AND type = :type'
                , array(':node'=>$node, ':type'=>$type));
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
                    AND (type = 'main' OR type IS NULL)
                    ";

            $query = self::query($sql, $values);
            foreach ( $query->fetchAll(\PDO::FETCH_CLASS) as $used) {
                unset($array[$used->item]);
            }
            return $array;
		}

        /*
         * Elementos disponibles apra portada
         */
		public static function availableSide ($node = \GOTEO_NODE) {
            if ($node == \GOTEO_NODE) {
                $array = array();
            } else {
                $array = self::$node_side_items;
            }
            $values = array(':node'=>$node);
            $sql = "SELECT
                        home.item as item
                    FROM home
                    WHERE home.node = :node
                    AND type = 'side'
                    ";

            $query = self::query($sql, $values);
            foreach ( $query->fetchAll(\PDO::FETCH_CLASS) as $used) {
                unset($array[$used->item]);
            }
            return $array;
		}

    }

}
