<?php
namespace Goteo\Model {

    use \Goteo\Library\Text,
        \Goteo\Model,
        \Goteo\Library\Check,
        \Goteo\Model\User,
        \Goteo\Model\Image;

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

                //Obtenemos el idioma de soporte
                $lang=self::default_lang_by_id($id, 'patron_lang', \LANG);

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
                        patron.active as `active`,
                        user.id as user_id,
                        user.name as user_name,
                        user.email as user_email,
                        user.avatar as user_avatar
                    FROM    patron
                    LEFT JOIN patron_lang
                        ON patron_lang.id = patron.id
                        AND patron_lang.lang = :lang
                    INNER JOIN project
                        ON project.id = patron.project
                    INNER JOIN user
                        ON user.id=patron.user
                    WHERE patron.id = :id
                    AND patron.node = :node
                    ", array(':id'=>$id, ':node'=>$node, ':lang'=>$lang));

                if($patron = $query->fetchObject(__CLASS__)) {

                    // datos del usuario. Eliminación de user::getMini
                    $user = new User;
                    $user->id = $patron->user_id;
                    $user->name = $patron->user_name;
                    $user->email = $patron->user_email;
                    $user->avatar = Image::get($patron->user_avatar);

                    $patron->user = $user;
                }

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

            if(self::default_lang(\LANG)=='es') {
                $different_select=" IFNULL(patron_lang.title, patron.title) as title,
                                    IFNULL(patron_lang.description, patron.description) as description";
                }
            else {
                $different_select=" IFNULL(patron_lang.title, IFNULL(patron.title, patron.title)) as title,
                                    IFNULL(patron_lang.description, IFNULL(patron.description, patron.description)) as description";
                $eng_join=" LEFT JOIN patron_lang as eng
                                ON  eng.id = patron.id
                                AND eng.lang = 'en'";
                }

            $sql="
                SELECT
                    patron.id as id,
                    patron.project as project,
                    project.name as name,
                    project.status as status,
                    patron.user as user,
                    $different_select,
                    patron.link as link,
                    patron_order.order as `order`,
                    patron.active as `active`,
                    user.id as user_id,
                    user.name as user_name,
                    user.email as user_email,
                    user.avatar as user_avatar
                FROM    patron
                LEFT JOIN patron_lang
                    ON patron_lang.id = patron.id
                    AND patron_lang.lang = :lang
                $eng_join
                LEFT JOIN patron_order
                    ON patron_order.id = patron.user
                INNER JOIN project
                    ON project.id = patron.project
                INNER JOIN user
                ON user.id = patron.user
                WHERE patron.node = :node
                $sqlFilter
                ORDER BY `order` ASC, name ASC
                ";

            $query = static::query($sql, array(':node' => $node, ':lang'=>\LANG));

            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $promo) {
                $promo->description =Text::recorta($promo->description, 100, false);

                // datos del usuario. Eliminación User::getMini

                $user = new User;
                $user->id = $promo->user_id;
                $user->name = $promo->user_name;
                $user->email = $promo->user_email;
                $user->avatar = Image::get($promo->user_avatar);
                $promo->user = $user;

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

             if(self::default_lang(\LANG)=='es') {
                $different_select=" IFNULL(patron_lang.title, patron.title) as title,
                                    IFNULL(patron_lang.description, patron.description) as patron_description";
                 $different_select_project=" IFNULL(project_lang.description, project.description) as description";
                }
            else {
                $different_select=" IFNULL(patron_lang.title, IFNULL(en.title, patron.title)) as title,
                                    IFNULL(patron_lang.description, IFNULL(en.description, patron.description)) as patron_description";
                $eng_join=" LEFT JOIN patron_lang as en
                                ON  en.id = patron.id
                                AND en.lang = 'en'";
                $different_select_project=" IFNULL(project_lang.description, IFNULL(eng.description, project.description)) as description";
                $eng_join_project=" LEFT JOIN project_lang as eng
                                    ON  eng.id = project.id
                                       AND eng.lang = 'en'";
                }

            $sql = "SELECT
                        patron.project as project,
                        $different_select,
                        project.name as name,
                        project.published as published,
                        project.created as created,
                        project.updated as updated,
                        project.success as success,
                        project.closed as closed,
                        project.mincost as mincost,
                        project.maxcost as maxcost,
                        $different_select_project,
                        project.amount as amount,
                        project.image as image,
                        project.num_investors as num_investors,
                        project.num_messengers as num_messengers,
                        project.num_posts as num_posts,
                        project.days as days,
                        user.id as user_id,
                        user.name as user_name,
                        project_conf.noinvest as noinvest,
                        project_conf.one_round as one_round,
                        project_conf.days_round1 as days_round1,
                        project_conf.days_round2 as days_round2,
                        project.status as status
                    FROM patron
                    LEFT JOIN patron_lang
                        ON patron_lang.id = patron.id
                        AND patron_lang.lang = :lang
                    $eng_join
                    INNER JOIN project
                        ON project.id = patron.project
                    LEFT JOIN project_lang
                        ON project_lang.id = project.id
                        AND project_lang.lang = :lang
                    $eng_join_project
                    INNER JOIN user
                        ON user.id = project.owner
                    LEFT JOIN project_conf
                        ON project_conf.project = project.id
                    WHERE patron.user = :user
                    $sqlFilter
                    ORDER BY patron.order ASC";
            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $reco) {
                try {
                    $reco->projectData = Project::getWidget($reco);
                } catch (\Goteo\Core\Error $e) {
                    continue;
                }
                $projects[] = $reco;
            }

            return $projects;
        }

        /**
         * Devuelve y actualiza el numero de proyectos recomendados por un padrino
         * deberá llamarse esta función cuando se actualizen las recomendaciones
         */
        public static function calcPatrons($user) {

            $values = array(':user' => $user);

            $count = self::query("SELECT
                    num_patron AS old_patron,
                    num_patron_active AS old_patron_active,
                    (SELECT count(*) FROM patron WHERE `user` = :user) AS num_patron,
                    (SELECT count(*) FROM patron WHERE `user` = :user AND active = 1) AS num_patron_active
                    FROM `user`
                    WHERE id = :user", $values);
            if($patrons = $count->fetchObject()) {
                if($patrons->num_patron != $patrons->old_patron || $patrons->num_patron_active != $patrons->old_patron_active) {
                    self::query("UPDATE
                        user SET
                        num_patron = :num_patron,
                        num_patron_active = :num_patron_active
                     WHERE id = :id", array(':id' => $user, ':num_patron' => $patrons->num_patron, ':num_patron_active' => $patrons->num_patron_active));
                }
            }
            return $patrons;
        }

        /*
         * Devuelve la lista de patronos con recomendaciones activas
         */
        public static function getActiveVips($node = \GOTEO_NODE) {

            $list = array();

            $values = array(':node'=>$node);

            $sql = "SELECT
                        patron.user as id,
                        user.name as name,
                        user.num_patron_active as num_patron_active,
                        user.avatar as user_avatar,
                        user_vip.image as vip_image
                    FROM patron
                    LEFT JOIN user_vip
                        ON user_vip.user = patron.user
                    LEFT JOIN user
                        ON user.id = patron.user
                    WHERE patron.active = 1
                    AND patron.node = :node
                    ORDER BY patron.`order` ASC";
            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $user = new User;
                $user->id = $item->id;
                $user->name = $item->name;
                // nos ahorramos las llamadas sql a image pues en la vista solo se usa el nombre y la id (de la funcion getLink)
                $user->avatar = Image::get($item->user_avatar);
                //si existe una imagen vip, la ponemos
                if($item->vip_image) {
                    $this->avatar = Image::get($item->vip_image);
                }
                //si no existe el numero de recomendaciones lo actualizamos
                $user->num_patron_active = $item->num_patron_active;
                if(!isset($item->num_patron_active)) {
                    $nums = self::calcPatrons($item->id);
                    $user->num_patron_active = $nums->num_patron_active;
                }
                $list[$item->id] = $user;
            }

            return $list;
        }

        /*
         * Devuelve la lista de patronos que deben estar en home
         *  con datos del usuario e imagen de padrino
         */
        public static function getInHome($node = \GOTEO_NODE) {

            $list = array();

            $values = array(':node'=>$node);

            try {
                $sql = "SELECT
                        user.id as id,
                        user.name as name,
                        user.num_patron_active as num_patron_active,
                        patron_order.order as `order`,
                        user_vip.image as vip_image,
                        user.avatar as user_avatar
                    FROM patron_order
                    INNER JOIN user
                        ON user.id = patron_order.id
                    LEFT JOIN user_vip
                        ON user_vip.user = patron_order.id
                    INNER JOIN patron
                        ON patron.user = patron_order.id
                        AND patron.node = :node
                    WHERE patron_order.order IS NOT NULL
                    GROUP BY patron.user
                    ORDER BY patron_order.order ASC";

                $query = self::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {

                    // usar avatar del usuario si no tiene imagen propia de vip (apaisada)
                    $item->avatar = (empty($item->vip_image) && !empty($item->user_avatar))
                        ? Model\Image::get($item->user_avatar)
                        : Model\Image::get($item->vip_image);

                    // solo en caso de que no tenga grabado el numero de proyectos apadrinados
                    if (!isset($item->num_patron_active)) {
                        $patrons = static::calcPatrons($item->id);
                        $item->num_patron = $patrons->num_patron;
                        $item->num_patron_active = $patrons->num_patron_active;
                    }

                    $list[$item->id] = $item;
                }

                return $list;
            } catch (\Goteo\Core\Error $e) {
                return array();
            }


        }

        /**
         * Devuelve las recomendaciones para un proyecto
         * para pintar los padrinos en la página de proyecto
         *
         * @param varchar50 $project
         * @param varchar50 $node  (para mostrar solo apadrinamientos en un mismo nodo)
         *
         */
        public static function getRecos($project, $node = null) {

            $recos = array();

            $values = array(':project'=>$project, ':lang'=>\LANG);

            if (!empty($node)) {
                $values[':node'] = $node;
                $sqlFilter = ' AND patron.node = :node';
            }

            $sql = "SELECT
                        patron.user,
                        IFNULL(patron_lang.title, patron.title) as title,
                        IFNULL(patron_lang.description, patron.description) as description,
                        patron.link as link,
                        user.id as user_id,
                        user.name as user_name,
                        user.email as user_email,
                        user.avatar as user_avatar
                    FROM patron
                    LEFT JOIN patron_lang
                        ON patron_lang.id = patron.id
                        AND patron_lang.lang = :lang
                    INNER JOIN user
                        ON user.id=patron.user
                    WHERE patron.project = :project
                    AND patron.active = 1
                    {$sqlFilter}
                    ORDER BY `order` ASC";
            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $reco) {
                // datos del usuario. Eliminación User::getMini

                $user = new User;
                $user->id = $reco['user_id'];
                $user->name = $reco['user_name'];
                $user->email = $reco['user_email'];
                $user->avatar = Image::get($reco['user_avatar']);

                $recoData = $user;

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
                //actualizar conteo
                self::calcPatrons($this->user);
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
        public function delete ($id = null) {
            if(empty($id) && $this->id) {
                $id = $this->id;
            }
            if(empty($id)) {
                // throw new Exception("Delete error: ID not defined!");
                return false;
            }
            $query = self::query("SELECT user FROM patron WHERE id = :id", array(':id' => $id));
            if($u = $query->fetchObject()) {
                $sql = "DELETE FROM patron WHERE id = :id";
                if (self::query($sql, array(':id' => $id))) {
                    //actualizar conteo
                    self::calcPatrons($u->user);
                    return true;
                }
            }
            return false;
        }

        /* Para activar/desactivar un apadrinamiento
         */
        public static function setActive ($id, $active = false) {
            $query = self::query("SELECT user FROM patron WHERE id = :id", array(':id' => $id));
            if($u = $query->fetchObject()) {
                $sql = "UPDATE patron SET active = :active WHERE id = :id";
                if (self::query($sql, array(':id'=>$id, ':active'=>$active))) {
                    //actualizar conteo
                    self::calcPatrons($u->user);
                    return true;
                }
            }
            return false;
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
