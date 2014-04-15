<?php
namespace Goteo\Model {

    use \Goteo\Library\Text,
        \Goteo\Model,
        \Goteo\Library\Check;

    class Patron extends \Goteo\Core\Model {

        public
            $id,
            $node,
            $project,
            $name,
            $user, // padrino
            $title,
            $description,
            $link,
            $order;

        /*
         *  Devuelve datos de un recomendado
         */
        public static function get ($id, $node = \GOTEO_NODE) {
                $query = static::query("
                    SELECT  
                        patron.id as id,
                        patron.node as node,
                        patron.project as project,
                        project.name as name,
                        patron.user as user,
                        IFNULL(patron_lang.title, patron.title) as title,
                        IFNULL(patron_lang.description, patron.description) as description,
                        patron.link as link,
                        patron.order as `order`,
                        patron.active as `active`
                    FROM    patron
                    LEFT JOIN patron_lang
                        ON patron_lang.id = patron.id
                        AND patron_lang.lang = :lang
                    INNER JOIN project
                        ON project.id = patron.project
                    WHERE patron.id = :id
                    AND patron.node = :node
                    ", array(':id'=>$id, ':node'=>$node, ':lang'=>\LANG));
                $patron = $query->fetchObject(__CLASS__);
                $patron->user = Model\User::getMini($patron->user);

                return $patron;
        }

        /*
         * Lista de proyectos recomendados
         * Para la gestión
         */
        public static function getAll ($node = \GOTEO_NODE, $activeonly = false) {

            // estados
            $status = Model\Project::status();

            $promos = array();

            $sqlFilter = ($activeonly) ? " AND patron.active = 1" : '';

            $query = static::query("
                SELECT
                    patron.id as id,
                    patron.project as project,
                    project.name as name,
                    project.status as status,
                    patron.user as user,
                    IFNULL(patron_lang.title, patron.title) as title,
                    IFNULL(patron_lang.description, patron.description) as description,
                    patron.link as link,
                    patron_order.order as `order`,
                    patron.active as `active`
                FROM    patron
                LEFT JOIN patron_lang
                    ON patron_lang.id = patron.id
                    AND patron_lang.lang = :lang
                LEFT JOIN patron_order
                    ON patron_order.id = patron.user
                INNER JOIN project
                    ON project.id = patron.project
                WHERE patron.node = :node
                $sqlFilter
                ORDER BY `order` ASC, name ASC
                ", array(':node' => $node, ':lang'=>\LANG));
            
            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $promo) {
                $promo->description =Text::recorta($promo->description, 100, false);
                $promo->home=self::in_home($promo->user);
                $promo->user = Model\User::getMini($promo->user);
                $promo->status = $status[$promo->status];
                $promos[] = $promo;
            }

            return $promos;
        }

        /**
         * Devuelve los proyectos recomendados por un padrino
         * para pintar en su página de perfil público
         *
         * @param varchar50 $user padrino
         */
        public static function getList($user, $activeonly = true) {

            $projects = array();

            $values = array(':user'=>$user, ':lang'=>\LANG);

            $sqlFilter = ($activeonly) ? " AND patron.active = 1" : '';

            $sql = "SELECT
                        project,
                        IFNULL(patron_lang.title, patron.title) as title,
                        IFNULL(patron_lang.description, patron.description) as description
                    FROM patron
                    LEFT JOIN patron_lang
                        ON patron_lang.id = patron.id
                        AND patron_lang.lang = :lang
                    WHERE user = :user
                    $sqlFilter
                    ORDER BY `order` ASC";
            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $reco) {
                try {
                    $reco->projectData = Project::getMedium($reco->project, LANG);
                } catch (\Goteo\Core\Error $e) {
                    continue;
                }
                $projects[] = $reco;
            }

            return $projects;
        }

        /*
         * Devuelve la lista de patronos con recomendaciones activas
         */
        public static function getActiveVips($node = \GOTEO_NODE) {

            $list = array();

            $values = array(':node'=>$node);

            $sql = "SELECT
                        patron.user as id
                    FROM patron
                    WHERE patron.active = 1
                    AND patron.node = :node
                    ORDER BY patron.`order` ASC";
            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                if (!in_array($item->id, $list))
                    $list[$item->id] = $item->id;
            }

            return $list;
        }

        /*
         * Devuelve la lista de patronos que deben estar en home
         */
        public static function getInHome($node = \GOTEO_NODE) {

            $list = array();

            $values = array(':node'=>$node);

            $sql = "SELECT
                        patron.user as id,
                        patron_order.order as `order`
                    FROM patron
                    LEFT JOIN patron_order
                        ON patron_order.id = patron.user
                        AND patron_order.order is not NULL
                    AND patron.node = :node
                    ORDER BY `order` ASC";                  

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                if (!in_array($item->id, $list))
                    $list[$item->id] = $item->id;
            }

            return $list;
        }

        /**
         * Devuelve las recomendaciones para un proyecto
         * para pintar los padrinos en la página de proyecto
         *
         * @param varchar50 $project
         */
        public static function getRecos($project, $node = \GOTEO_NODE) {

            $recos = array();

            $values = array(':node' => $node, ':project'=>$project, ':lang'=>\LANG);

            $sql = "SELECT
                        user,
                        IFNULL(patron_lang.title, patron.title) as title,
                        IFNULL(patron_lang.description, patron.description) as description,
                        patron.link as link
                    FROM patron
                    LEFT JOIN patron_lang
                        ON patron_lang.id = patron.id
                        AND patron_lang.lang = :lang
                    WHERE patron.project = :project
                    AND patron.active = 1
                    AND patron.node = :node
                    ORDER BY `order` ASC";
            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $reco) {
                $recoData = Model\User::getMini($reco['user']);
                $vipData = Model\User\Vip::get($reco['user']);
                if (!empty($vipData->image)) {
                    $recoData->avatar = $vipData->image;
                }
                $recoData->title = $reco['title'];
                $recoData->description = $reco['description'];
                if (empty($reco['link'])) {
                    $recoData->link = '/user/profile/'.$reco['user'];
                } else {
                    $recoData->link = $reco['link'];
                }
                $recos[] = $recoData;
            }

            return $recos;
        }

        /*
         * Solo contar
         */
        public static function numRecos($project) {

            $values = array(':project'=>$project);

            $sql = "SELECT
                        COUNT(user) as num
                    FROM patron
                    WHERE patron.project = :project
                    AND patron.active = 1
                    ";
            $query = self::query($sql, $values);
            return $query->fetchColumn();
        }

        /*
         * Lista de proyectos disponibles para recomendar
         */
        public static function available ($current = null, $node = \GOTEO_NODE) {

            $values = array();
            if (!empty($current)) {
                $sqlCurr = " OR project.id = :id";
                $values[':id'] = $current;
            } else {
                $sqlCurr = "";
            }

            if ($node != \GOTEO_NODE) {
                $sqlFilter = " AND project.node = :node";
                $values[':node'] = $node;
            } else {
                $sqlFiler = "";
            }

            $sql = "
                SELECT
                    project.id as id,
                    project.name as name,
                    project.status as status
                FROM    project
                WHERE status = 3
                $sqlFilter
                $sqlCurr
                ORDER BY name ASC
                ";
//                AND project.id NOT IN (SELECT project FROM patron WHERE patron.node = :node{$sqlCurr} )
            $query = static::query($sql, $values);

            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }


        public function validate (&$errors = array()) { 
            if (empty($this->node))
                $errors[] = 'Falta nodo';

            if (empty($this->project))
                $errors[] = 'Falta proyecto';

            if (empty($this->user))
                $errors[] = 'Falta padrino';

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
                'project',
                'user',
                'title',
                'description',
                'link',
                'active',
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
                $sql = "REPLACE INTO patron SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /* Para reordenar un padrino
         */
        public static function setOrder ($id, $num) {

            $sql = "UPDATE patron SET `order` = :num WHERE user = :id";
            if (self::query($sql, array(':id'=>$id, ':num'=>$num))) {
                return true;
            } else {
                return false;
            }

        }


        /*
         * Para quitar un apadrinamiento
         */
        public static function delete ($id) {
            
            $sql = "DELETE FROM patron WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /* Para activar/desactivar un apadrinamiento
         */
        public static function setActive ($id, $active = false) {

            $sql = "UPDATE patron SET active = :active WHERE id = :id";
            if (self::query($sql, array(':id'=>$id, ':active'=>$active))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para poner un padrino en home
         */
        public static function add_home ($id) {
            
            if(!self::in_home($id))
            
            {
                $order=self::next_easy();

                $sql = "INSERT INTO patron_order (`id`, `order`) VALUES (:id, :order)";
                
                if (self::query($sql, array(':id'=>$id,':order'=>$order))) {
                    return true;
                } else {
                    return false;
                }
            }

            else
                return true;
        }


        /*
         * Para quitar un padrino de home
         */

        public static function remove_home ($id) {
            
            $sql = "DELETE FROM patron_order WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que salga antes  (disminuir el order)
         */
        public static function up ($id) {
            
            return Check::reorder($id, 'up', 'patron_order', 'id', 'order');
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($id) {
           
            return Check::reorder($id, 'down', 'patron_order', 'id', 'order');
        }

        /*
         * Para que un proyecto salga antes  (disminuir el order)
         **
        public static function up ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'up', 'patron_order', 'id', 'order', $extra);
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         *
        public static function down ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'down', 'patron_order', 'id', 'order', $extra);
        }
        */

        // orden para siguiente padrino

        public static function next_easy () {
            $query = self::query('SELECT MAX(`order`) FROM patron_order');
            $order = $query->fetchColumn(0);
            return ++$order;

        }

        // comprobar si un padrino está en home
        public static function in_home ($id) {

         $query = self::query('SELECT `order` FROM patron_order WHERE id = :id'
                    , array(':id'=>$id));
        
        if($order=$query->fetchColumn(0)) 
            return $order;
        
        else return 0;    
        }

        // orden para siguiente apadrinamiento

        public static function next ($user = null, $node = \GOTEO_NODE) {
            if (isset($user)) {
                $query = self::query('SELECT `order` FROM patron WHERE user = :user'
                    , array(':user'=>$user));
                $order = $query->fetchColumn(0);

                if (empty($order)) {
                    $query = self::query('SELECT MAX(`order`) FROM patron WHERE node = :node'
                        , array(':node'=>$node));
                    $order = $query->fetchColumn(0);
                    return ++$order;
                }

                return $order;
            } else {
                $query = self::query('SELECT MAX(`order`) FROM patron WHERE node = :node'
                    , array(':node'=>$node));
                $order = $query->fetchColumn(0);
                return ++$order;
            }

        }


    }
    
}