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

use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Core\Model;
use Goteo\Library\Text;
use Goteo\Model\Project;
use Goteo\Model\User;
use Goteo\Model\Invest;
use Goteo\Model\Sphere;
use Goteo\Model\Image;
use Goteo\Library\Check;

class Stories extends \Goteo\Core\Model {

    public
        $id,
        $node,
        $project = null,
        $lang,
        $order,
        $image,
        $title,
        $description,
        $review,
        $url,
        $pool = false,
        $pool_image,
        $post,
        $active = false,
        $type,
        $landing_match = 0,
        $landing_pitch = 0,
        $sphere,
        $background_image
        ;


    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);

        if(empty($this->node)) $this->node = Config::get('node');
        if(empty($this->order)) $this->order = self::next($this->node);
        if(empty($this->lang)) $this->lang = Config::get('sql_lang');
    }
    public static function getLangFields() {
        return ['title', 'description', 'review'];
    }

    /*
     *  Devuelve datos de una historia exitosa
     */
    public static function get ($id, $lang = null) {

        list($fields, $joins) = self::getLangsSQLJoins($lang);

        $query = static::query("
            SELECT
                stories.id as id,
                stories.node as node,
                stories.project as project,
                stories.lang as lang,
                $fields,
                stories.url as url,
                stories.image as image,
                stories.pool_image as pool_image,
                stories.pool as pool,
                stories.text_position as text_position,
                stories.order as `order`,
                stories.post as `post`,
                stories.active as `active`,
                stories.type as `type`,
                stories.landing_pitch as `landing_pitch`,
                stories.landing_match as `landing_match`,
                stories.sphere as `sphere`,
                stories.background_image as `background_image`,

                project.id as project_id,
                project.name as project_name,
                project.amount as project_amount,
                project.num_investors as project_num_investors,

                user.id as user_id,
                user.name as user_name

            FROM    stories
            LEFT JOIN project
                ON project.id = stories.project
            LEFT JOIN user
                ON user.id = project.owner
            $joins
            WHERE stories.id = :id
            ", [':id' => $id]);

        return $query->fetchObject(__CLASS__);
    }

    /*
     * Lista de historias exitosas
     * TODO: reform this (or getList better with pagination when StoriesAdmin module is created)
     */
    public static function getAll ($activeonly = false, $pool= false, $filters = [], $node = \GOTEO_NODE) {

        // estados
        $status = Project::status();

        $stories = array();

        $sqlFilter = ($activeonly) ? " AND stories.active = 1" : '';

        $sqlFilter.= ($pool) ? " AND stories.pool = 1" : '';

        if (!empty($filters['landing_match'])) {
             $sqlFilter.= " AND stories.landing_match = 1";
        }

        if (!empty($filters['landing_pitch'])) {
             $sqlFilter.= " AND stories.landing_pitch = 1";
        }

        if (!empty($filters['type'])) {
             $sqlFilter.= " AND type = '".$filters['type']."'";
        }
        if (!empty($filters['project'])) {
             $sqlFilter.= " AND stories.project = '".$filters['project']."'";
        }
        if (!empty($filters['project_owner'])) {
             $sqlFilter.= " AND project.owner = '".$filters['project_owner']."'";
        }

        $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang);

        $query = static::query("
            SELECT
                stories.id as id,
                stories.node as node,
                stories.project as project,
                stories.lang as lang,
                $fields,
                stories.url as url,
                stories.image as image,
                stories.pool_image as pool_image,
                stories.pool as pool,
                stories.text_position as text_position,
                stories.order as `order`,
                stories.post as `post`,
                stories.active as `active`,
                stories.type as `type`,
                stories.landing_pitch as `landing_pitch`,
                stories.landing_match as `landing_match`,
                stories.sphere as `sphere`,
                stories.background_image as `background_image`,
                project.id as project_id,
                project.name as project_name,
                project.amount as project_amount,
                project.num_investors as project_num_investors,

                user.id as user_id,
                user.name as user_name
            FROM    stories
            LEFT JOIN project
                ON project.id = stories.project
            LEFT JOIN user
                ON user.id = project.owner
            $joins
            WHERE stories.node = :node
            $sqlFilter
            GROUP BY id
            ORDER BY `order` ASC
            ", array(':node' => $node));

        foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $story) {
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

    /**
     * Histories listing
     *
     * @param array filters
     * @param string node id
     * @param int limit items per page or 0 for unlimited
     * @param int page
     * @param int pages
     * @return array of project instances
     */
    static public function getList($filters = [], $offset = 0, $limit = 10, $count = false, $lang = null) {

        $values = [];
        $sqlFilters = [];
        $sql = '';

        foreach(['owner', 'active', 'landing_match', 'landing_pitch', 'pool', 'node', 'type'] as $key) {
            if (isset($filters[$key])) {
                $filter[] = "stories.$key = :$key";
                $values[":$key"] = $filters[$key];
            }
        }

        if(isset($filters['project'])) {
            $filter[] = "stories.project LIKE :project";
            $values[":project"] = '%' . $filters['project'] . '%';
        }
        if(isset($filters['project_owner'])) {
            $filter[] = "stories.project_owner = :project_owner";
            $values[":project_owner"] = $filters['project_owner'];
        }

        foreach(['id', 'title', 'description', 'review'] as $key) {
            if (isset($filters[$key])) {
                $filter[] = "stories.$key LIKE :$key";
                $values[":$key"] = '%'.$filters[$key].'%';
            }
        }
        if($filters['superglobal']) {
            $filter[] = "(stories.title LIKE :superglobal OR stories.description LIKE :superglobal OR stories.review LIKE :superglobal)";
            $values[':superglobal'] = '%'.$filters['superglobal'].'%';
        }
        if($filters['supersuperglobal']) {
            $filter[] = "(stories.title LIKE :superglobal OR stories.description LIKE :superglobal OR stories.review LIKE :superglobal)";
            $values[':superglobal'] = '%'.$filters['superglobal'].'%';
        }
        // print_r($filter);die;
        if($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        if($count) {
            // Return count
            $sql = "SELECT COUNT(id) FROM stories$sql";
            // echo \sqldbg($sql, $values);
            return (int) self::query($sql, $values)->fetchColumn();
        }

        $offset = (int) $offset;
        $limit = (int) $limit;

        if(!$lang) $lang = Lang::current();
        $values['viewLang'] = $lang;
        list($fields, $joins) = self::getLangsSQLJoins($lang);

        $sql ="SELECT
                stories.id as id,
                stories.node as node,
                stories.project as project,
                stories.lang as lang,
                $fields,
                stories.url as url,
                stories.image as image,
                stories.pool_image as pool_image,
                stories.pool as pool,
                stories.text_position as text_position,
                stories.order as `order`,
                stories.post as `post`,
                stories.active as `active`,
                stories.type as `type`,
                stories.landing_pitch as `landing_pitch`,
                stories.landing_match as `landing_match`,
                stories.sphere as `sphere`,
                stories.background_image as `background_image`,

                project.id as project_id,
                project.name as project_name,
                project.amount as project_amount,
                project.num_investors as project_num_investors,

                user.id as user_id,
                user.name as user_name,
                :viewLang as viewLang
            FROM stories
            LEFT JOIN project
                ON project.id = stories.project
            LEFT JOIN user
                ON user.id = project.owner
            $joins
            $sql
            ORDER BY `order` ASC
            LIMIT $offset,$limit";

        // print_r($values);die(\sqldbg($sql, $values));
        if($query = self::query($sql, $values)) {
            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }
        return [];
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

        if(empty($this->title))
            $errors[] = "Missing title";

        return empty($errors);
    }

    // returns the current project
    public function getProject() {
        if(isset($this->projectObject)) return $this->projectObject;
        try {
            $this->projectObject = Project::get($this->project);
        } catch(ModelNotFoundException $e) {
            $this->projectObject = false;
        }
        return $this->projectObject;
    }

    public function getUser() {
        if(!$this->userInstance instanceOf User) {
            $user_id = $this->user_id ? $this->user_id : null;
            if($user_id)
                $this->userInstance = User::get($user_id);
            elseif($this->getProject())
                return $this->getProject()->getOwner();
        }
        return $this->userInstance;
    }

    public function getImage() {
        if(!$this->imageInstance instanceOf Image && $this->image) {
            $this->imageInstance = new Image($this->image);
        }
        return $this->imageInstance;
    }

    /**
     * Return sphere
    */
    public function getSphere() {
        if(!$this->sphereInstance instanceOf Sphere) {
            $this->sphereInstance = Sphere::get($this->sphere);
        }
        return $this->sphereInstance;
    }

    public function getPoolImage() {
        if(!$this->PoolImageInstance instanceOf Image && $this->pool_image) {
            $this->PoolImageInstance = new Image($this->pool_image);
        }
        return $this->PoolImageInstance;
    }

    public function getBackgroundImage() {
        if(!$this->BackgroundImageInstance instanceOf Image && $this->background_image) {
            $this->BackgroundImageInstance = new Image($this->background_image);
        }
        return $this->BackgroundImageInstance;
    }

    public function save (&$errors = array()) {
        if (!$this->validate($errors)) return false;


        // This is obsolete, keeping while the old stories admin module is active
        // Imagen de fondo de stories
        if(is_array($this->image)&&empty($this->image['name'])) {
            $this->image = reset($this->image);
        }
        if (is_array($this->image) && !empty($this->image['name'])||($this->image instanceOf Image && $this->image->tmp)) {
            $image = new Image($this->image);
            if ($image->save($errors)) {
                $this->image = $image->id;
            } else {
                \Goteo\Application\Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                $this->image = '';
            }
        }
        // Imagen de landing monedero
        if(is_array($this->pool_image)&&empty($this->pool_image['name'])) {
            $this->pool_image = reset($this->pool_image);
        }
        if (is_array($this->pool_image) && !empty($this->pool_image['name'])||($this->pool_image instanceOf Image && $this->pool_image->tmp)) {
            $pool_image = new Image($this->pool_image);
            if ($pool_image->save($errors)) {
                $this->pool_image = $pool_image->id;
            } else {
                \Goteo\Application\Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                $this->image = '';
            }
        }

        // Background image in channel call view
        if(is_array($this->background_image)&&empty($this->background_image['name'])) {
            $this->background_image = reset($this->background_image);
        }
        if (is_array($this->background_image) && !empty($this->background_image['name'])||($this->background_image instanceOf Image && $this->background_image->tmp)) {
            $background_image = new Image($this->background_image);
            if ($background_image->save($errors)) {
                $this->background_image = $background_image->id;
            } else {
                \Goteo\Application\Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                $this->image = '';
            }
        }

        $fields = array(
            'id',
            'node',
            'project',
            'lang',
            'order',
            'image',
            'pool_image',
            'pool',
            'text_position',
            'active',
            'type',
            'landing_pitch',
            'landing_match',
            'sphere',
            'title',
            'description',
            'review',
            'url',
            'post',
            'background_image'
            );

        try {
            $this->dbInsertUpdate($fields);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Stories save error: " . $e->getMessage();
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

    /*
     *  List of types
     */
    public static function getListTypes(){
        return Config::get('stories.types');
    }

}
