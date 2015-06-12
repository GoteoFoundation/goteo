<?php
namespace Goteo\Model {

    use Goteo\Library\Text,
        Goteo\Model\Project,
        Goteo\Model\Image,
        Goteo\Application\Lang,
        Goteo\Application\Config,
        Goteo\Library\Check;

    class Banner extends \Goteo\Core\Model {

        public
            $id,
            $node,
            $project,
            $image,
            $order;

        /*
         *  Devuelve datos de un banner de proyecto
         */
        public static function get ($id, $lang = null) {

                //Obtenemos el idioma de soporte
                $lang=self::default_lang_by_id($id, "banner_lang", $lang);

                $query = static::query("
                    SELECT
                        banner.id as id,
                        banner.node as node,
                        banner.project as project,
                        project.name as name,
                        IFNULL(banner_lang.title, banner.title) as title,
                        IFNULL(banner_lang.description, banner.description) as description,
                        banner.url as url,
                        banner.image as image,
                        banner.order as `order`,
                        banner.active as `active`
                    FROM    banner
                    LEFT JOIN banner_lang
                        ON  banner_lang.id = banner.id
                        AND banner_lang.lang = :lang
                    LEFT JOIN project
                        ON project.id = banner.project
                    WHERE banner.id = :id
                    ", array(':id'=>$id, ':lang' => $lang));
                if($banner = $query->fetchObject('\Goteo\Model\Banner')) {
                    $banner->image = Image::get($banner->image);
                }

                return $banner;
        }

        /**
         * Lista de proyectos en banners
         * La funcion Banner::getAll esta en los archivos:
         * controller/index.php OK
         * controller/admin/banners.php (PARECE OK, FALTA COMPROBAR en el admin: parece que se usa en view/admin/banners/list.html.php pero solo usa los campos de la tabla banner)
         * view/node/header.html.php (PARECE OK, FALTA COMPROBAR en el nodo, parece que se usa en view/node/banners.html.php campos: url, title, description, image->name )
         */
        public static function getAll ($activeonly = false, $node = \GOTEO_NODE) {

            // estados
            $status = Project::status();

            $banners = array();

            $sqlFilter = ($activeonly) ? " AND banner.active = 1" : '';

            if(self::default_lang(Lang::current()) === Config::get('lang')) {
                $different_select=" IFNULL(banner_lang.title, banner.title) as title,
                                    IFNULL(banner_lang.description, banner.description) as description";
                }
                else {
                    $different_select=" IFNULL(banner_lang.title, IFNULL(eng.title, banner.title)) as title,
                                        IFNULL(banner_lang.description, IFNULL(eng.description, banner.description)) as description";
                    $eng_join=" LEFT JOIN banner_lang as eng
                                    ON  eng.id = banner.id
                                    AND eng.lang = 'en'";
                }

                // sacamos también los datos de proyecto que se necesitan
                $sql="SELECT
                        banner.id as id,
                        banner.node as node,
                        banner.project as project,
                        project.name as name,
                        $different_select,
                        banner.url as url,
                        project.status as status,
	                    project.name as project_name,
	                    project.days as project_days,
	                    project.amount as project_amount,
	                    project.mincost as project_mincost,
	                    project.maxcost as project_maxcost,
	                    user.name as project_user_name,
                        banner.image as image,
                        banner.order as `order`,
                        banner.active as `active`
                    FROM    banner
                    LEFT JOIN project
                        ON project.id = banner.project
	                LEFT JOIN user
	                    ON user.id = project.owner
                    LEFT JOIN banner_lang
                        ON  banner_lang.id = banner.id
                        AND banner_lang.lang = :lang
                    $eng_join
                    WHERE banner.node = :node
                    $sqlFilter
                    ORDER BY `order` ASC";

            $query = static::query($sql, array(':node' => $node, ':lang' => Lang::current()));

            $used_projects = array();
            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $banner) {
                // ahora Image::get ya no hace consulta sql porque el nombre de la imagene stá en la tabla
                $banner->image = Image::get($banner->image);
                $banner->status = $status[$banner->status];

                //mincost, maxcost, si mincost es zero, lo calculamos:
                if(!empty($banner->project) && empty($banner->project_mincost)) {
                    $calc = Project::calcCosts($banner->project);
                    $banner->project_mincost = $calc->mincost;
                    $banner->project_maxcost = $calc->maxcost;
                    //a partir de aqui ya deberia estar calculado para las siguientes consultas
                }

                //rewards, metodo antiguo un sql por proyecto
                // $banner->project_social_rewards = Project\Reward::getAll($banner->project, 'social', Lang::current());
                //
                // usado para obtener los rewards de golpe
                if (!empty($banner->project)) $used_projects[$banner->project] = $banner->id;
                $banners[$banner->id] = $banner;
            }

            // rewards es un array, podemo llamarlo directamente para los proyectos implicados
            // REWARDS: la vista banner.html.php usa: (id, reward, icon, license)
            // Nota: añadido el campo "project" en la tabla "reward" como indice para acelerar las busquedas
            $query = static::query("
                SELECT
                reward.id,
                reward.project,
                reward.icon,
                reward.license,
                IFNULL(reward_lang.reward, reward.reward) as reward
                FROM reward
                LEFT JOIN reward_lang
                    ON  reward_lang.id = reward.id
                    AND reward_lang.lang = :lang
                    AND reward_lang.project = reward.project
                WHERE
                reward.project IN ('" . implode("','", array_keys($used_projects)) . "')
                AND type = :type", array('lang' => Lang::current(), 'type' => 'social'));
            //añadir a cada banner:
            foreach($query->fetchAll(\PDO::FETCH_CLASS) as $reward){
                $banners[$used_projects[$reward->project]]->project_social_rewards[$reward->id] = $reward;
            }
            return $banners;
        }

        /*
         * Lista de banners
         */
        public static function getList ($node = \GOTEO_NODE) {

            $banners = array();
            // solo banenrs de nodo
            if ($node == \GOTEO_NODE) {
                return false;
            }

            $query = static::query("
                SELECT
                    banner.id as id,
                    banner.node as node,
                    banner.title as title,
                    banner.description as description
                FROM    banner
                WHERE banner.node = :node
                ORDER BY `order` ASC
                ", array(':node' => $node));

            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $banner) {
                $banners[] = $banner;
            }

            return $banners;
        }

        /*
         * Lista de proyectos disponibles para destacar
         */
        public static function available ($current = null, $node = \GOTEO_NODE) {

            if (!empty($current)) {
                $sqlCurr = " AND banner.project != '$current'";
            } else {
                $sqlCurr = "";
            }

            $query = static::query("
                SELECT
                    project.id as id,
                    project.name as name,
                    project.status as status
                FROM    project
                WHERE status = 3
                AND project.id NOT IN (SELECT project FROM banner WHERE banner.node = :node AND project IS NOT NULL {$sqlCurr} )
                ORDER BY name ASC
                ", array(':node' => $node));

            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }

        // ya no validamos esto,  puede haber banners in proyecto y sin imagen
        public function validate (&$errors = array()) {
            if (empty($this->project))
                $errors[] = 'Falta proyecto';

            if (empty($this->image))
                $errors[] = 'Falta imagen';

            if (empty($errors))
                return true;
            else
                return false;
        }

        /**
         * Static compatible version of parent delete()
         * @param  [type] $id [description]
         * @return [type]     [description]
         */
        public function delete($id = null) {
            if(empty($id)) return parent::delete();

            if(!($ob = Banner::get($id))) return false;
            return $ob->delete();

        }

        public function save (&$errors = array()) {
//            if (!$this->validate($errors)) return false;

            // Imagen de fondo de banner
            if (is_array($this->image) && !empty($this->image['name'])) {
                $image = new Image($this->image);

                if ($image->save($errors)) {
                    $this->image = $image->id;
                } else {
                    \Goteo\Application\Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                    $this->image = '';
                }
            }
            if (is_null($this->image)) {
                $this->image = '';
            }

            $fields = array(
                'id',
                'node',
                'title',
                'description',
                'url',
                'project',
                'image',
                'order',
                'active'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO banner SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /* Para activar/desactivar un banner
         */
        public static function setActive ($id, $active = false) {

            $sql = "UPDATE banner SET active = :active WHERE id = :id";
            if (self::query($sql, array(':id'=>$id, ':active'=>$active))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que un proyecto salga antes  (disminuir el order)
         */
        public static function up ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'up', 'banner', 'id', 'order', $extra);
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'down', 'banner', 'id', 'order', $extra);
        }

        /*
         *
         */
        public static function next ($node = \GOTEO_NODE) {
            $query = self::query('SELECT MAX(`order`) FROM banner WHERE node = :node'
                , array(':node'=>$node));
            $order = $query->fetchColumn(0);
            return ++$order;

        }


    }

}
