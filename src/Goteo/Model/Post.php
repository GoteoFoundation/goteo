<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model {

    use Goteo\Model\Project\Media,
        Goteo\Model\Image,
        Goteo\Model\Project,
        Goteo\Model\User,
        Goteo\Model\Node,
        Goteo\Application\Lang,
        Goteo\Application\Config,
        Goteo\Library\Check;

    class Post extends \Goteo\Core\Model {

        public
            $id,
            $title,
            $blog,
            $date,
            $text,
            $image,
            $gallery = array(), // array de instancias image de post_image
            $media,
            $author,
            $order,
            $publish,
            $home = false,
            $node;  // las entradas en portada para nodos se guardan en la tabla post_node con unos metodos alternativos

        /*
         *  Devuelve datos de una entrada
         */
        public static function get ($id, $lang = null) {

                //Obtenemos el idioma de soporte
                $lang=self::default_lang_by_id($id, 'post_lang', $lang);

                $sql = "SELECT
                        post.id as id,
                        IFNULL(post_lang.title, post.title) as title,
                        IFNULL(post_lang.text, post.text) as `text`,
                        post.blog as blog,
                        post.image as image,
                        post.media as `media`,
                        post.date as `date`,
                        DATE_FORMAT(post.date, '%d | %m | %Y') as fecha,
                        post.author as author,
                        post.order as `order`,
                        post.publish as `publish`,
                        user.id as user_id,
                        user.name as user_name,
                        user.email as user_email,
                        user.avatar as user_avatar
                    FROM    post
                    LEFT JOIN post_lang
                        ON  post_lang.id = post.id
                        AND post_lang.lang = :lang
                        AND post_lang.blog = post.blog
                    LEFT JOIN user
                        ON user.id=post.author
                    WHERE post.id = :id
                    ";
                $values = array(':id' => $id, ':lang'=>$lang);
                // die(\sqldbg($sql, $values));
                $query = static::query($sql, $values);

                $post = $query->fetchObject(__CLASS__);

                // video
                if (isset($post->media)) {
                    $post->media = new Media($post->media);
                }

                if($post instanceOf \Goteo\Model\Post) {
                    $post->gallery = Image::getModelGallery('post', $post->id);
                    $post->image = Image::getModelImage($post->image, $post->gallery);
                }

                // autor
                if (!empty($post->author)) {

                    // datos del usuario. Eliminación de user::getMini
                    $user = new User;
                    $user->id = $post->user_id;
                    $user->name = $post->user_name;
                    $user->email = $post->user_email;
                    $user->avatar = Image::get($post->user_avatar);

                    $post->user = $user;
                }
                return $post;

        }

        /*
         * Lista de entradas
         */
        public static function getAll ($position = 'home', $node = \GOTEO_NODE) {

            if (!in_array($position, array('home', 'footer'))) {
                $position = 'home';
            }

            $list = array();

            $values = array(':lang'=>Lang::current());

            if ($node == \GOTEO_NODE || empty($node)) {
                // portada goteo, sacamos todas las de blogs tipo nodo
                // que esten marcadas en la tabla post
                $sqlFilter = " WHERE post.$position = 1
                    AND post.publish = 1
                    ";
                $sqlField = "post.order as `order`,";

            } else {
                // portada nodo, igualmente las entradas de blogs tipo nodo
                // perosolo la que esten en la tabla de entradas en portada de ese nodo
                $sqlFilter = " WHERE post.id IN (SELECT post FROM post_node WHERE node = :node)
                    AND post.publish = 1
                    ";
                $values[':node'] = $node;

                $sqlField = "(SELECT `order` FROM post_node WHERE node = :node AND post = post.id) as `order`,";
            }

            if(Lang::current() === Config::get('lang')) {
                $different_select=" IFNULL(post_lang.title, post.title) as title,
                                    IFNULL(post_lang.text, post.text) as `text`";
                }
            else {
                    $different_select=" IFNULL(post_lang.title, IFNULL(eng.title, post.title)) as title,
                                        IFNULL(post_lang.text, IFNULL(eng.text, post.text)) as `text`";
                    $eng_join=" LEFT JOIN post_lang as eng
                                    ON  eng.id = post.id
                                    AND eng.lang = 'en'";
                }

            $sql = "
                SELECT
                    post.id as id,
                    post.blog as blog,
                    $different_select,
                    post.image as `image`,
                    post.media as `media`,
                    $sqlField
                    DATE_FORMAT(post.date, '%d-%m-%Y') as date,
                    DATE_FORMAT(post.date, '%d | %m | %Y') as fecha,
                    post.publish as publish,
                    post.author as author,
                    post.home as home,
                    post.footer as footer,
                    blog.type as owner_type,
                    blog.owner as owner_id,
                    user.id as user_id,
                    user.name as user_name,
                    user.email as user_email,
                    user.avatar as user_avatar,
                    user.node as user_node
                FROM    post
                INNER JOIN blog
                    ON  blog.id = post.blog
                LEFT JOIN post_lang
                    ON  post_lang.id = post.id
                    AND post_lang.lang = :lang
                    AND post_lang.blog = post.blog
                LEFT JOIN user
                    ON user.id=post.author
                $eng_join
                $sqlFilter
                ORDER BY `order` ASC, title ASC
                ";

            $query = static::query($sql, $values);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $post) {

                $post->media = new Media($post->media);

                $post->gallery = Image::getModelGallery('post', $post->id);
                $post->image = Image::getModelImage($post->image, $post->gallery);

                $post->type = $post->home == 1 ? 'home' : 'footer';

                // datos del autor
                switch ($post->owner_type) {
                    case 'project':
                        $proj_blog = Project::getMini($post->owner_id);
                        $post->author = $proj_blog->owner;
                        $post->user   = $proj_blog->user;
                        $post->owner_name = $proj_blog->name;
                        //esto solo hacerlo si hace falta
                        if($post->author != $proj_blog->owner) {
                            $sql = "UPDATE post SET author = :owner WHERE post.id = :id";
                            self::query($sql, [':id' => $post->id, ':owner' => $proj_blog->owner]);
                        }
                        break;

                    case 'node':

                        // datos del usuario. Eliminación de user::getMini

                        $user = new User;
                        $user->id = $post->user_id;
                        $user->name = $post->user_name;
                        $user->email = $post->user_email;
                        $user->node = $post->user_node;
                        $user->avatar = Image::get($post->user_avatar);

                        $post->user = $user;
                        /*
                        $node_blog = Node::get($post->owner_id);
                        $post->owner_name = $node_blog->name;
                         *
                         */
                        break;
                }

                $list[$post->id] = $post;
            }

            return $list;
        }

        /*
         * Entradas en portada o pie
         */
        public static function getList ($position = 'home', $node = \GOTEO_NODE) {

            if (!in_array($position, array('home', 'footer'))) {
                $position = 'home';
            }

            $list = array();

            $values = array(':lang'=>Lang::current());

            if ($node == \GOTEO_NODE || empty($node)) {
                // portada goteo, sacamos todas las de blogs tipo nodo
                // que esten marcadas en la tabla post
                $sqlFilter = " WHERE post.$position = 1
                ";

            } else {
                // portada nodo, igualmente las entradas de blogs tipo nodo
                // perosolo la que esten en la tabla de entradas en portada de ese nodo
                $sqlFilter = " WHERE post.id IN (SELECT post FROM post_node WHERE node = :node)
                    ";
                $values[':node'] = $node;
            }

            if(Lang::current() === Config::get('lang')) {
                $different_select=" IFNULL(post_lang.title, post.title) as title";
                }
            else {
                    $different_select=" IFNULL(post_lang.title, IFNULL(eng.title, post.title)) as title";
                    $eng_join=" LEFT JOIN post_lang as eng
                                    ON  eng.id = post.id
                                    AND eng.lang = 'en'";
                }

            $sql = "
                SELECT
                    post.id as id,
                    $different_select,
                    post.order as `order`
                FROM    post
                INNER JOIN blog
                    ON  blog.id = post.blog
                LEFT JOIN post_lang
                    ON  post_lang.id = post.id
                    AND post_lang.lang = :lang
                    AND post_lang.blog = post.blog
                $eng_join
                $sqlFilter
                ORDER BY `order` ASC, title ASC
                ";

            $query = static::query($sql, $values);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $post) {
                $list[$post->id] = $post->title;
            }

            return $list;
        }

        /*
         * Usado en el autocompletado de
         * http://devgoteo.org/admin/stories/edit/1
         */
        public static function getAutocomplete () {
            $list = array();

            if(Lang::current() === Config::get('lang')) {
                $different_select=" IFNULL(post_lang.title, post.title) as title";
                }
            else {
                    $different_select=" IFNULL(post_lang.title, IFNULL(eng.title, post.title)) as title";
                    $eng_join=" LEFT JOIN post_lang as eng
                                    ON  eng.id = post.id
                                    AND eng.lang = 'en'";
                 }

            $query = static::query("
                SELECT
                    post.id as id,
                    $different_select
                FROM    post
                LEFT JOIN post_lang
                    ON  post_lang.id = post.id
                    AND post_lang.lang = :lang
                    AND post_lang.blog = post.blog
                $eng_join
                ", array(':lang'=>Lang::current()));

            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $post) {
                $list[$post->id] = $post->title;
            }

            return $list;
        }

        public function validate (&$errors = array()) {
            if (empty($this->title))
                $errors[] = 'Falta título';
                //Text::get('mandatory-post-title');

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            if(!$this->date) $this->date = date('Y-m-d');

            $fields = array(
                'blog',
                'title',
                'text',
                'date',
                'media',
                'legend',
                'order',
                'publish',
                'home',
                'footer',
                'author'
                );

            try {
                //automatic $this->id assignation
                $this->dbInsertUpdate($fields);

                return true;
            } catch(\PDOException $e) {
                $errors[] = "Error saving Post " . $e->getMessage();
                return false;
            }
        }

        /*
         *  Actualizar una entrada en portada
         * si es de nodo se guarda en otra tabla con el metodo update_node
         */
        public function update (&$errors = array()) {
            if (!$this->id) return false;

            $fields = array(
                'order',
                'home',
                'footer'
                );

            $set = '';
            $values = array(':id'=>$this->id);

            foreach ($fields as $field) {
                if (!isset ($this->$field))
                    continue;

                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            if ($set == '') {
                $errors[] = 'Sin datos';
                return false;
            }

            try {
                $sql = "UPDATE post SET " . $set . " WHERE post.id = :id";
                self::query($sql, $values);

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar una entrada
         */
        public static function remove ($id, $from = null) {

            if (!in_array($from, array('home', 'footer'))) {
                return false;
            }

            $sql = "UPDATE post SET `$from`=0, `order`=NULL WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que salga antes  (disminuir el order)
         */
        public static function up ($id, $type = 'home') {
            $extra = array (
                    $type => 1
                );
            return Check::reorder($id, 'up', 'post', 'id', 'order', $extra);
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($id, $type = 'home') {
            $extra = array (
                    $type => 1
                );
            return Check::reorder($id, 'down', 'post', 'id', 'order', $extra);
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next ($type = 'home') {
            $query = self::query('SELECT MAX(`order`) FROM post WHERE '.$type.'=1');
            $order = $query->fetchColumn(0);
            return ++$order;

        }


        /****************************************************
        * Variantes de los metodos para las portadas de nodo *
         ****************************************************/
        /*
         *  Actualizar una entrada en portada
         */
        public function update_node ($data, &$errors = array()) {
            if (!$data->post || !$data->node) return false;

            $fields = array(
                'post',
                'node',
                'order'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $data->$field;
            }

            if ($set == '') {
                $errors[] = 'Sin datos';
                return false;
            }

            try {
                $sql = "REPLACE INTO post_node SET " . $set;
                self::query($sql, $values);

                return true;
            } catch(\PDOException $e) {
                $errors[] = "Ha fallado!!! " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar una entrada
         */
        public static function remove_node ($post, $node) {

            $values = array(':post'=>$post, ':node'=>$node);
            $sql = "DELETE FROM post_node WHERE post = :post AND node = :node";
            if (self::query($sql, $values)) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que salga antes  (disminuir el order)
         */
        public static function up_node ($post, $node) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($post, 'up', 'post_node', 'post', 'order', $extra);
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down_node ($post, $node) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($post, 'down', 'post_node', 'post', 'order', $extra);
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next_node ($node) {
            $query = self::query('SELECT MAX(`order`) FROM post_node WHERE node = :node', array(':node'=>$node));
            $order = $query->fetchColumn(0);
            return ++$order;

        }

    }

}
