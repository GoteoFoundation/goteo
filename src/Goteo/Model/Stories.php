<?php
namespace Goteo\Model {

    use Goteo\Application\Lang;
    use Goteo\Application\Config;
    use Goteo\Core\Model;
    use Goteo\Library\Text,
        Goteo\Model\Project,
        Goteo\Model\User,
        Goteo\Model\Invest,
        Goteo\Model\Image,
        Goteo\Library\Check;

    class Stories extends \Goteo\Core\Model {

        public
            $id,
            $node,
            $project = null,
            $order,
            $image,
            $title,
            $description,
            $review,
            $url,
            $pool = false,
            $pool_image,
            $post,
            $active = false;

        /*
         *  Devuelve datos de una historia exitosa
         */
        public static function get ($id, $lang = null) {

                //Obtenemos el idioma de soporte
                $lang = self::default_lang_by_id($id, 'stories_lang', $lang);

                $query = static::query("
                    SELECT
                        stories.id as id,
                        stories.node as node,
                        stories.project as project,
                        IFNULL(stories_lang.title, stories.title) as title,
                        IFNULL(stories_lang.description, stories.description) as description,
                        IFNULL(stories_lang.review, stories.review) as review,
                        stories.url as url,
                        stories.image as image,
                        stories.pool_image as pool_image,
                        stories.pool as pool,
                        stories.text_position as text_position,
                        stories.order as `order`,
                        stories.post as `post`,
                        stories.active as `active`,

                        project.id as project_id,
                        project.name as project_name,
                        project.amount as project_amount,
                        project.num_investors as project_num_investors,
                        project.id as project_id,

                        user.id as user_id,
                        user.name as user_name

                    FROM    stories
                    LEFT JOIN project
                        ON project.id = stories.project
                    LEFT JOIN user
                        ON user.id = project.owner
                    LEFT JOIN stories_lang
                        ON  stories_lang.id = stories.id
                        AND stories_lang.lang = :lang
                    WHERE stories.id = :id
                    ", array(':id'=>$id, ':lang' => $lang));
                if($story = $query->fetchObject(__CLASS__)) {
                    $story->image = !empty($story->image) ? Image::get($story->image) : null;

                    $story->pool_image = !empty($story->pool_image) ? Image::get($story->pool_image) : null;

                    $user = new User;
                    $user->id = $story->user_id;
                    $user->name = $story->user_name;

                    $project = new Project;
                    $project->id = $story->project_id;
                    $project->name = $story->project_name;
                    $project->amount = $story->project_amount;
                    $project->num_investors = $story->project_num_investors;
                    $project->user = $user;

                    if(empty($project->amount)) {
                        $project->amount = Invest::invested($project->id);
                    }
                    if(empty($project->num_investors)) {
                        $project->num_investors = Invest::numInvestors($project->id);
                    }


                    $story->project = $project;
                }
                return $story;
        }

        /*
         * Lista de historias exitosas
         */
        public static function getAll ($activeonly = false, $pool= false, $node = \GOTEO_NODE) {

            // estados
            $status = Project::status();

            $stories = array();

            $sqlFilter = ($activeonly) ? " AND stories.active = 1" : '';

            $sqlFilter.= ($pool) ? " AND stories.pool = 1" : '';

            if(self::default_lang(Lang::current()) === Config::get('lang')) {
                $different_select=" IFNULL(stories_lang.title, stories.title) as title,
                                    IFNULL(stories_lang.description, stories.description) as description,
                                    IFNULL(stories_lang.review, stories.review) as review,
                                    IFNULL(open_tag_lang.name, open_tag.name) as open_tags_name";
                }
            else {
                    $different_select=" IFNULL(stories_lang.title, IFNULL(eng.title, stories.title)) as title,
                                        IFNULL(stories_lang.description, IFNULL(eng.description, stories.description)) as description,
                                        IFNULL(stories_lang.review, IFNULL(eng.review, stories.review)) as review,
                                        IFNULL(open_tag_lang.name, IFNULL(eng_open_tag.name, open_tag.name)) as open_tags_name";
                    $eng_join=" LEFT JOIN stories_lang as eng
                                    ON  eng.id = stories.id
                                    AND eng.lang = 'en'";

                    $eng_join_open_tags=" LEFT JOIN open_tag_lang as eng_open_tag
                                    ON  eng_open_tag.id = open_tag.id
                                    AND eng_open_tag.lang = 'en'";
                }

            $query = static::query("
                SELECT
                    stories.id as id,
                    stories.node as node,
                    stories.project as project,
                    $different_select,
                    stories.url as url,
                    stories.image as image,
                    stories.pool_image as pool_image,
                    stories.pool as pool,
                    stories.text_position as text_position,
                    stories.order as `order`,
                    stories.post as `post`,
                    stories.active as `active`,
                    open_tag.post as open_tags_post,

                    project.id as project_id,
                    project.name as project_name,
                    project.amount as project_amount,
                    project.num_investors as project_num_investors,
                    project.id as project_id,

                    user.id as user_id,
                    user.name as user_name
                FROM    stories
                LEFT JOIN project
                    ON project.id = stories.project
                LEFT JOIN user
                    ON user.id = project.owner
                LEFT JOIN stories_lang
                    ON  stories_lang.id = stories.id
                    AND stories_lang.lang = :lang
                $eng_join
                LEFT JOIN project_open_tag
                    ON  project_open_tag.project = stories.project
                LEFT JOIN open_tag
                    ON  open_tag.id = project_open_tag.open_tag
                LEFT JOIN open_tag_lang
                    ON  open_tag_lang.id = open_tag.id
                    AND open_tag_lang.lang = :lang
                $eng_join_open_tags
                WHERE stories.node = :node
                $sqlFilter
                GROUP BY id
                ORDER BY `order` ASC
                ", array(':node' => $node, ':lang' => Lang::current()));

            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $story) {
                $story->image = !empty($story->image) ? Image::get($story->image) : null;

                if(!empty($story->pool_image))
                    $story->pool_image = Image::get($story->pool_image);
                else
                    $story->pool_image = new Image();


                $story->status = $status[$story->status];

                $user = new User;
                $user->id = $story->user_id;
                $user->name = $story->user_name;

                $project = new Project;
                $project->id = $story->project_id;
                $project->name = $story->project_name;
                $project->amount = $story->project_amount;
                $project->num_investors = $story->project_num_investors;
                $project->user = $user;

                if(empty($project->amount)) {
                    $project->amount = Invest::invested($project->id);
                }
                if(empty($project->num_investors)) {
                    $project->num_investors = Invest::numInvestors($project->id);
                }


                $story->project = $project;
                $stories[] = $story;
            }

            return $stories;
        }

        /*
         * Lista de historias exitosas
         */
        public static function getList ($node = \GOTEO_NODE) {

            $stories = array();

            $query = static::query("
                SELECT
                    stories.id as id,
                    stories.node as node,
                    stories.title as name,
                    stories.order as `order`,
                    stories.post as `post`,
                    stories.active
                FROM stories
                WHERE stories.node = :node
                ORDER BY `order` ASC
                ", array(':node' => $node));

            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $story) {
                $stories[] = $story;
            }

            return $stories;
        }

        /*
         * Lista de proyectos disponibles para destacar
         */
        public static function available ($current = null, $node = \GOTEO_NODE) {

            if (!empty($current)) {
                $sqlCurr = " AND stories.project != '$current'";
            } else {
                $sqlCurr = "";
            }

            $query = static::query("
                SELECT
                    project.id as id,
                    project.name as name,
                    project.status as status
                FROM    project
                WHERE ( status = 5 OR status = 4 )
                ORDER BY name ASC
                ", array(':node' => $node));

            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }

        public function validate (&$errors = array()) {

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            // Imagen de fondo de stories
            if (is_array($this->image) && !empty($this->image['name'])) {
                $image = new Image($this->image);

                if ($image->save($errors)) {
                    $this->image = $image->id;
                } else {
                    \Goteo\Application\Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                    $this->image = '';
                }
            }

            // Imagen de landing monedero
            if (is_array($this->pool_image) && !empty($this->pool_image['name'])) {
                $pool_image = new Image($this->pool_image);

                if ($pool_image->save($errors)) {
                    $this->pool_image = $pool_image->id;
                } else {
                    \Goteo\Application\Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                    $this->image = '';
                }
            }

            $fields = array(
                'id',
                'node',
                'project',
                'order',
                'image',
                'pool_image',
                'pool',
                'text_position',
                'active',
                'title',
                'description',
                'review',
                'url',
                'post'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO stories SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /* Para activar/desactivar una historia exitosa
         */
        public static function setActive ($id, $active = false) {

            $sql = "UPDATE stories SET active = :active WHERE id = :id";
            if (self::query($sql, array(':id'=>$id, ':active'=>$active))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que una historia salga antes (disminuir el order)
         */
        public static function up ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'up', 'stories', 'id', 'order', $extra);
        }

        /*
         * Para que una historia salga después (aumentar el order)
         */
        public static function down ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'down', 'stories', 'id', 'order', $extra);
        }

        /*
         *
         */
        public static function next ($node = \GOTEO_NODE) {
            $query = self::query('SELECT MAX(`order`) FROM stories WHERE node = :node'
                , array(':node'=>$node));
            $order = $query->fetchColumn(0);
            return ++$order;

        }


    }

}
