<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

namespace Goteo\Model\Blog;

use Goteo\Application\Config;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Lang;
use Goteo\Core\Model;
use Goteo\Library\Text;
use Goteo\Model\Image;
use Goteo\Model\Project\Media;
use Goteo\Model\User;
use PDOException;

class Post extends Model {

    public
        $id,
        $blog,
        $slug,
        $project,
        $title,
        $subtitle,
        $text,
        $image,
        $header_image,
        $media,
        $legend,
        $date,
        $home,
        $footer,
        $author,
        $owner,
        $owner_id,
        $allow = false,
        $publish = false,
        $type = 'md', // html or md (markdown)
        $tags = array(),
        $section,
        $gallery = array(), // array de instancias image de post_image
        $num_comments = 0,
        $comments = array();


    public static function sanitizeText($t) {
        return strip_tags($t, '<br><a><strong><i><b><ul><li><ol><em><blockquote><p><img><code><pre><h1><h2><h3><h4><h5><h6><small>');
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

        // This model does not automatically request translation support language, only if requested
        // That's because Projects can be in any custom language and its
        // corresponding blog will match the same language as main

        if(!$model_lang) $model_lang = Config::get('lang');
        list($fields, $joins) = self::getLangsSQLJoins($lang, $model_lang);

        $sql = "
            SELECT
                post.id as id,
                post.blog as blog,
                post.slug as slug,
                $fields,
                post.image as `image`,
                post.media as `media`,
                post.header_image as `header_image`,
                post.section as `section`,
                post.date as `date`,
                post.type as `type`,
                DATE_FORMAT(post.date, '%d | %m | %Y') as fecha,
                post.allow as allow,
                post.publish as publish,
                post.home as home,
                post.footer as footer,
                CONCAT(blog.type, '-', blog.owner) as owner,
                post.num_comments as num_comments,
                blog.type as owner_type,
                blog.owner as owner_id,
                IFNULL ( project.owner, post.author ) as author,
                IFNULL( impulsor.name, user.name ) as user_name,
                IFNULL (project.name, node.name )  as owner_name
            FROM    post
            INNER JOIN blog
                ON  blog.id = post.blog
            $joins
            LEFT JOIN user
                ON user.id=post.author
            LEFT JOIN project
                    ON project.id = blog.owner
                    AND blog.type = 'project'
            LEFT JOIN user as impulsor
                  ON impulsor.id = project.owner
                  AND blog.type = 'project'
            LEFT JOIN node
                    ON node.id = blog.owner
                    AND blog.type = 'node'
            ";

        if(is_string($id)) {
            $sql .= "WHERE post.slug = :slug ";
            $values = [':slug' => $id];
        } else {
            $sql .= "WHERE post.id = :id";
            $values = [':id' => $id];
        }

        $query = static::query($sql, $values);
        $post = $query->fetchObject(__CLASS__);

        // Try by slug in the event that the slug is a number
        if(!$post instanceof Post) {
            return false;
        }

        $post->user   = new User;
        $post->user->name = $post->user_name;
        $post->gallery = Image::getModelGallery('post', $post->id);
        $post->image = Image::getModelImage($post->image, $post->gallery);
        $post->header_image = Image::getModelImage($post->header_image);

        // video
        if (isset($post->media)) {
            $post->media = new Media($post->media);
        }

        $post->comments = Post\Comment::getAll($post->id);

        if (!isset($post->num_comments)) {
            $post->num_comments = Post\Comment::getCount($post->id);
        }

        //tags
        $post->tags = Post\Tag::getAll($post->id);

        // @deprecated: This should be in the controller if needed
        // $post->text = self::sanitizeText($post->text);

        return $post;
    }

    /*
     * Lista de entradas
     * de mas nueva a mas antigua
     * // si es portada son los que se meten por la gestion de entradas en portada que llevan el tag 1 'Portada'
     */
    public static function getAll ($blog = null, $limit = null, $published = true, $model_lang = null) {
        $list = array();

        $values = [];
        if(!$model_lang) $model_lang = Config::get('lang');
        list($fields, $joins) = self::getLangsSQLJoins(Lang::current(), $model_lang);

        $sql = "
            SELECT
                post.id as id,
                post.blog as blog,
                post.slug as slug,
                blog.type as type,
                blog.owner as owner,
                $fields,
                post.image as `image`,
                post.header_image as `header_image`,
                post.section as `section`,
                post.type as `type`,
                DATE_FORMAT(post.date, '%d-%m-%Y') as date,
                DATE_FORMAT(post.date, '%d | %m | %Y') as fecha,
                post.publish as publish,
                post.home as home,
                post.footer as footer,
                post.num_comments as num_comments,
                blog.type as owner_type,
                blog.owner as owner_id,
                IFNULL ( project.owner, post.author ) as author,
                IFNULL( impulsor.name, user.name ) as user_name,
                IFNULL (project.name, node.name )  as owner_name
            FROM    post
            INNER JOIN blog
                ON  blog.id = post.blog
            LEFT JOIN user
                    ON user.id=post.author
            $joins
            LEFT JOIN project
                    ON project.id = blog.owner
                    AND blog.type = 'project'
            LEFT JOIN user as impulsor
                  ON impulsor.id = project.owner
                  AND blog.type = 'project'
            LEFT JOIN node
                    ON node.id = blog.owner
                    AND blog.type = 'node'
            ";
        if (!empty($blog)) {
            $sql .= " WHERE post.blog = :blog
            ";
            $values[':blog'] = $blog;
        } else {
            $sql .= " WHERE blog.type = 'node'
            ";
        }
        // solo las entradas publicadas
        if ($published) {
            $sql .= " AND post.publish = 1
            ";
            if (empty($blog)) {
            $sql .= " AND node.active = 1
                AND blog.owner != 'testnode'
            ";
            }
        }
        $sql .= "ORDER BY post.date DESC, post.id DESC
            ";
        if (!empty($limit)) {
            $sql .= "LIMIT $limit";
        }

        $query = static::query($sql, $values);

        foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $post) {

            $post->user   = new User;
            $post->user->name = $post->user_name;

            $post->gallery = Image::getModelGallery('post', $post->id);
            $post->image = Image::getModelImage($post->image, $post->gallery);

            // video
            if (!empty($post->media)) {
                $post->media = new Media($post->media);
            }

            if (!isset($post->num_comments)) {
                  $post->num_comments = Post\Comment::getCount($post->id);
            }

            $post->tags = Post\Tag::getAll($post->id);

            $list[$post->id] = $post;
        }

        return $list;
    }

    // returns the author
    public function getAuthor() {
        if($this->userInstance) return $this->userInstance;
        $this->userInstance = User::get($this->author);
        return $this->userInstance;
    }

    public function getSlug() {
        return $this->slug ? $this->slug : $this->id;
    }

    /*
     * Lista de entradas filtradas
     *  por tag
     * de mas nueva a mas antigua
     */
    public static function getList ($filters = array(), $published = true, $offset = 0, $limit = 10, $count = false, $lang = null, $model_lang = null) {
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
                post.num_comments as num_comments,
                blog.type as owner_type,
                blog.owner as owner_id,
                IFNULL ( project.owner, post.author ) as author,
                IFNULL( impulsor.name, user.name ) as user_name,
                IFNULL (project.name, node.name )  as owner_name
            FROM    post
            INNER JOIN blog
                ON  blog.id = post.blog
            $joins
            LEFT JOIN user
                    ON user.id=post.author
            LEFT JOIN project
                    ON project.id = blog.owner
                    AND blog.type = 'project'
            LEFT JOIN user as impulsor
                  ON impulsor.id = project.owner
                  AND blog.type = 'project'
            LEFT JOIN node
                    ON node.id = blog.owner
                    AND blog.type = 'node'
            ";

        $sqlWhere = '';

        if(!is_array($filters)) {
            if(is_integer($filters))
                $filters = ['show' => 'all', 'blog' => $filters];
            else
                $filters = ['show' => $filters];
        }
        if (in_array($filters['show'], array('all', 'home', 'footer'))) {
            $sqlWhere = " WHERE blog.id IS NOT NULL
            ";
        } elseif ($filters['show'] == 'updates') {
            $sqlWhere = " WHERE blog.type = 'project'
            ";
        } else {
            $sqlWhere = " WHERE blog.type = 'node'
            ";
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
        if ($published || $filters['show'] == 'published') {
            $sqlWhere .= " AND post.publish = 1
            ";
            if (empty($filters['blog'])) {
            $sqlWhere .= " AND blog.owner IN (SELECT id FROM node WHERE active = 1)
                AND blog.owner != 'testnode'
            ";
            }
        }

        // solo las del propio blog
        if ($filters['show'] == 'owned') {
            $sqlWhere .= " AND blog.owner = :node
            ";
            $values[':node'] = $filters['node'];
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
                INNER JOIN blog ON  blog.id = post.blog
                $sqlWhere";
            return (int) self::query($sql, $values)->fetchColumn();
        }

        $sql .= "$sqlWhere
            ORDER BY post.date DESC, post.id DESC
            LIMIT $offset, $limit
            ";

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

    public function validate (&$errors = array()) {
        if (empty($this->title))
            $errors['title'] = 'Falta título';

        if (empty($this->text))
            $errors['text'] = 'Falta texto';

        if (empty($this->date))
            $errors['date'] = 'Falta fecha';

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
        if (empty($this->blog)) return false;

        if(!$this->date) $this->date = date('Y-m-d');

        // Attempt to create slug if not exists
        if(!$this->slug) {
            $this->slug = self::idealiza($this->title, false, false, 150);
            if($this->slug && $this->slugExists($this->slug)) {
                $this->slug = $this->slug .'-' . ($this->id ? $this->id : time());
            }
        }

        $fields = array(
            // 'id',
            'blog',
            'slug',
            'title',
            'subtitle',
            'section',
            'text',
            'media',
            'header_image',
            'legend',
            'date',
            'allow',
            'publish',
            'home',
            'footer',
            'author',
            'type'
            );

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);

            // Luego la imagen
            // Will be an Upload if it's not Image
            if($this->image) {
                if(is_array($this->image) && !$this->image['tmp_name']) {
                    try {
                        Image::replaceGallery('post', $this->id, $this->image);
                    } catch(ModelException $e) {
                        throw new PDOException(Text::get('gallery-upload-fail')." (".$e->getMessage().")");
                    }
                    $this->gallery = $this->image;
                    $this->image = $this->image ? $this->image[0] : null;
                } else {
                    // Old behaviour, add the image to the gallery if
                    // needed (it's an upload)
                    $img = $this->image;
                    if(!$img instanceOf Image) {
                        $img = new Image($img);
                    }
                    if (!$img->addToModelGallery('post', $this->id)) {
                        throw new PDOException(Text::get('image-upload-fail'));
                    }
                    $this->gallery[] = $img;
                    $this->image = $img;
                }

                // Rebuild default image
                if($this->image) $this->image->setModelImage('post', $this->id);
                else             Image::deleteModelImage('post', $this->id);
            }

            // y los tags, si hay
            if (is_array($this->tags)) {
                static::query('DELETE FROM post_tag WHERE post= ?', $this->id);
                foreach ($this->tags as $tag) {
                    $new = new Post\Tag([
                            'post' => $this->id,
                            'tag' => $tag
                        ]);
                    $new->assign($errors);
                    unset($new);
                }
            }

            // actualizar campo calculado
            if ( $this->publish == 1 && $this->owner_type == 'project' ) {
                self::numPosts($this->owner_id);
            }

            return true;
        } catch(PDOException $e) {
            $errors[] = $e->getMessage();
            return false;
        }
    }

    public static function getLangFields() {
        return ['title', 'subtitle', 'text', 'legend'];
    }

    /*
     *  Para saber si una entrada permite comentarios
     */
    public static function allowed ($id) {
            $query = static::query("
                SELECT
                    allow
                FROM    post
                WHERE id = :id
                ", array(':id' => $id));

            $post = $query->fetchObject(__CLASS__);

            if ($post->allow > 0) {
                return true;
            } else {
                return false;
            }
    }

    /*
     * Numero de entradas de novedaades (publicadads) de un proyecto
     */
    public static function numPosts ($project, $published_only = true) {

        $debug = false;

        $values = array(':project' => $project);

        $sql = "SELECT  COUNT(*) as posts, project.num_posts as num
            FROM    post
            INNER JOIN project
                ON project.id = :project
            INNER JOIN blog
                ON blog.owner = project.id
                AND blog.type = 'project'
            WHERE post.blog = blog.id
            ";

        if ($published_only)
            $sql .= 'AND post.publish = 1';

        if ($debug) {
            echo \trace($values);
            echo $sql;
            die;
        }

        $query = static::query($sql, $values);
        if($got = $query->fetchObject()) {
            // si ha cambiado, actualiza el numero de inversores en proyecto
            if ($got->posts != $got->num) {
                static::query("UPDATE project SET num_posts = :num WHERE id = :project", array(':num' => (int) $got->posts, ':project' => $project));
            }
        }

        return (int) $got->posts;
    }

    // List of blog sections
    public static function getListSections() {
        return Config::get('blog.sections');
    }

    public static function getSection($section){
        $sections = self::getListSections();
        return $sections[$section];
    }
}
