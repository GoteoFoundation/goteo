<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

namespace Goteo\Model;

use Goteo\Model\Project\Media;
use Goteo\Model\Image;
use Goteo\Model\Project;
use Goteo\Model\User;
use Goteo\Model\Node;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Library\Check;

class Post extends \Goteo\Core\Model {

    public
        $id,
        $title,
        $subtitle,
        $blog,
        $slug,
        $date,
        $text,
        $image,
        $header_image,
        $gallery = array(), // array de instancias image de post_image
        $media,
        $author,
        $order,
        $publish,
        $section,
        $home = false,
        $node;  // las entradas en portada para nodos se guardan en la tabla post_node con unos metodos alternativos


    public static function getLangFields() {
        return ['title', 'subtitle', 'text', 'legend'];
    }
    
    // fallbacks to getbyid
    public static function getBySlug($slug, $lang = null, $model_lang = null) {
        $post = self::get((string)$slug, $lang, $model_lang);
        if(!$post) {
            $post = self::get((int)$slug, $lang, $model_lang);
        }
        return $post;
    }

    public static function getById($id, $lang = null, $model_lang = null) {
        return self::get((int)$id, $lang, $model_lang);
    }


    /*
     *  Devuelve datos de una entrada
     */
    public static function get ($id, $lang = null, $model_lang = null) {

        // This model does not automaticalley request translation
        // support language only if requested
        // That's because Projects can be in any custom language and its
        // corresponding blog will match the same language as main

        if(!$model_lang) $model_lang = Config::get('lang');
        list($fields, $joins) = self::getLangsSQLJoins($lang, $model_lang, null, 'Goteo\Model\Blog\Post');

        $sql = "SELECT
                post.id as id,
                $fields,
                post.blog as blog,
                post.slug as slug,
                post.image as image,
                post.header_image as `header_image`,
                post.section as `section`,
                post.media as `media`,
                post.type as `type`,
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
            $joins
            LEFT JOIN user
                ON user.id=post.author
            ";
        if(is_string($id)) {
            $sql .= "WHERE post.slug = :slug";
            $values = [':slug' => $id];
        } else {
            $sql .= "WHERE post.id = :id";
            $values = [':id' => $id];
        }


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
     * Lista de entradas filtradas
     *  por tag
     * de mas nueva a mas antigua
     */
    public static function getFilteredList ($filters = array(), $offset = 0, $limit = 10, $count = false, $lang = null, $model_lang = null) {
        if(!$lang) $lang = Lang::current();
        if(!$model_lang) $model_lang = Config::get('lang');
        $values = [];

        $list = [];

        list($fields, $joins) = self::getLangsSQLJoins($lang, $model_lang);


        $offset = (int) $offset;
        $limit = (int) $limit;

        $sql = "
            SELECT
                post.id as id,
                post.blog as blog,
                post.slug as slug,
                $fields,
                post.image as `image`,
                post.type as `type`,
                post.section as `section`,
                post.glossary as `glossary`,
                post.header_image as `header_image`,
                post.media as `media`,
                post.date as `date`,
                DATE_FORMAT(post.date, '%d-%m-%Y') as fecha,
                post.publish as publish,
                post.home as home,
                post.footer as footer,
                post.num_comments as num_comments
            FROM    post
            $joins
            ";

        $sqlWhere = '';

        if(!is_array($filters)) {
            if(is_integer($filters))
                $filters = ['show' => 'all', 'blog' => $filters];
            else
                $filters = ['show' => $filters];
        }
        if (!empty($filters['blog'])) {
            $sqlWhere = " WHERE post.blog = :blog
            ";
            $values[':blog'] = $filters['blog'];
        }

        if (!empty($filters['superglobal'])) {
            $sqlWhere .= " AND (post.id LIKE :qid OR post.slug LIKE :superglobal OR post.title LIKE :superglobal OR post.subtitle LIKE :superglobal OR post.text LIKE :superglobal)";
            $values[':qid'] = $filters['superglobal'];
            $values[':superglobal'] = '%' . $filters['superglobal'] . '%';
        }
        if (!empty($filters['tag'])) {
            $sqlWhere .= " AND post.id IN (SELECT post FROM post_tag WHERE tag = :tag)
            ";
            $values[':tag'] = $filters['tag'];
        }

        // Filter by workshop
        if (!empty($filters['workshop'])) {
            $sqlWhere .= " AND post.id IN (SELECT post_id FROM workshop_post WHERE workshop_id = :workshop)
            ";
            $values[':workshop'] = $filters['workshop'];
        }

        // Filter by node
        if (!empty($filters['node'])) {
            $sqlWhere .= " AND post.id IN (SELECT post_id FROM node_post WHERE node_id = :node)
            ";
            $values[':node'] = $filters['node'];
        }

        if (!empty($filters['section'])) {
            $sqlWhere .= " AND post.section = :section
            ";
            $values[':section'] = $filters['section'];
        }

        // Post excluded from the
        if (!empty($filters['excluded'])) {
            $sqlWhere .= " AND post.id  != :excluded
            ";
            $values[':excluded'] = $filters['excluded'];
        }

        if (!empty($filters['author'])) {
            $sqlWhere .= " AND post.author = :author
            ";
            $values[':author'] = $filters['author'];
        }

        // solo las publicadas
        if ($filter['published'] || $filters['show'] == 'published') {
            $sqlWhere .= " AND post.publish = 1
            ";
        }

        // solo las de la portada
        if ($filters['show'] == 'home') {
            if ($filters['node'] == Config::get('node')) {
                $sqlWhere .= " AND post.home = 1
                ";
            } else {
                $sqlWhere .= " AND post.id IN (SELECT post FROM post_node WHERE node = :node)
                ";
                $values[':node'] = $filters['node'];
            }
        }

        if ($filters['show'] == 'footer') {
            if ($filters['node'] == Config::get('node')) {
                $sqlWhere .= " AND post.footer = 1
                ";
            }
        }

        if($count) {
            // Return count
            $sql = "SELECT COUNT(post.id)
                FROM post
                $sqlWhere";
            return (int) self::query($sql, $values)->fetchColumn();
        }

        $sql .= "$sqlWhere
            ORDER BY post.date DESC, post.id DESC
            LIMIT $offset, $limit
            ";

        // die(\sqldbg($sql, $values));
        $query = static::query($sql, $values);

        foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $post) {

            $post->user   = new User;
            $post->user->name = $post->user_name;

            $post->gallery = Image::getModelGallery('post', $post->id);
            $post->image = Image::getModelImage($post->image, $post->gallery);
            $post->header_image = Image::getModelImage($post->header_image);

            // video
            if (isset($post->media)) {
                $post->media = new Media($post->media);
            }

            if (!isset($post->num_comments)) {
                $post->num_comments = Post\Comment::getCount($post->id);
            }

            $list[$post->id] = $post;
        }

        return $list;
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
                post.slug as slug,
                $different_select,
                post.image as `image`,
                post.header_image as `header_image`,
                post.section as `section`,
                post.media as `media`,
                post.type as `type`,
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
                post.slug as slug,
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

    public function getSlug() {
        return $this->slug ? $this->slug : $this->id;
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
                post.slug as slug,
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

    public function slugExists($slug) {
        $values = [':slug' => $slug];
        $sql = 'SELECT COUNT(*) FROM post WHERE slug=:slug';
        if($this->id) {
            $values[':id'] = $this->id;
            $sql .= ' AND id!=:id';
        }

        return self::query($sql, $values)->fetchColumn() > 0;
    }

    public function save (&$errors = array()) {
        if (!$this->validate($errors)) return false;

        if(!$this->date) $this->date = date('Y-m-d');
        // Attemp to create slug if not exists
        if(!$this->slug) {
            $this->slug = self::idealiza($this->title, false, false, 150);
            if($this->slug && $this->slugExists($this->slug)) {
                $this->slug = $this->slug .'-' . ($this->id ? $this->id : time());
            }
        }


        $fields = array(
            'blog',
            'slug',
            'title',
            'text',
            'date',
            'media',
            'image',
            'header_image',
            'legend',
            'order',
            'publish',
            'home',
            'footer',
            'author',
            'type'
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
            $errors[] = $e->getMessage();
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

    // List of blog sections
    public static function getListSections(){
        return Config::get('blog.sections');
    }

    public static function getSection($section){
        $sections = self::getListSections();
        return $sections[$section];
    }

}

