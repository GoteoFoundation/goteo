<?php

/*
* Model for tax workshop
*/

namespace Goteo\Model;

use Datetime;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Core\Model;
use Goteo\Model\Workshop\WorkshopLocation;
use Goteo\Model\Workshop\WorkshopSponsor;
use Goteo\Model\Blog\Post as GeneralPost;
use PDO;


class Workshop extends Model {

    public
    $id,
    $title,
    $subtitle,
    $online,
    $blockquote,
    $event_type,
    $description,
    $date_in,
    $date_out,
    $schedule,
    $url,
    $header_image,
    $venue,
    $city,
    $venue_address,
    $how_to_get,
    $map_iframe,
    $schedule_file_url,
    $call_id,
    $workshop_location,
    $lang,
    $modified,
    $type;


    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);

        if(empty($this->lang)) $this->lang = Config::get('sql_lang');
    }

    public static function getLangFields() {
        return ['title', 'subtitle', 'blockquote', 'description', 'schedule', 'how_to_get'];
    }

    /**
     * Get data about a workshop
     *
     * @param   int    $id         workshop id.
     * @return  Workshop object
     */
    static public function get($id, $lang = null, $model_lang = null) {

        if(!$model_lang) $model_lang = Config::get('lang');
        list($fields, $joins) = self::getLangsSQLJoins($lang, $model_lang);

        $sql="SELECT
                    workshop.id,
                    workshop.online,
                    workshop.date_in,
                    workshop.date_out,
                    workshop.schedule_file_url,
                    workshop.terms_file_url,
                    $fields,
                    workshop.url,
                    workshop.call_id,
                    workshop.venue,
                    workshop.city,
                    workshop.venue_address,
                    workshop.header_image,
                    workshop.map_iframe,
                    workshop.event_type
              FROM workshop
              $joins
              WHERE workshop.id = ?";

        $query = static::query($sql, array($id));
        $item = $query->fetchObject(__CLASS__);

        if($item) {
            return $item;
        }

        throw new ModelNotFoundException("Workshop not found for ID [$id]");
    }

    /**
     * @param  array  $filters
     * @return mixed            Array of workshops
     */
    public static function getAll($filters = array()) {

        $lang = Lang::current();
        $values = array();
        $list = array();
        $sqlJoin = "";

        if ($filters['call']) {
            $sqlFilter = 'workshop.call_id = :call';
            $values[':call'] = $filters['call'];
        }

        if (isset($filters['node'])) {
            $sqlJoin .= "INNER JOIN node_workshop ON node_workshop.workshop_id = workshop.id and node_workshop.node_id = :node ";
            $values[":node"] = $filters['node'];
        }

        if (is_array($filters['event_type'])) {
            $parts = [];
            foreach($filters['type'] as $i => $type) {
                    $parts[] = ':event_type' . $i;
                    $values[':event_type' . $i] = $type;
            }
            if($parts) $sqlFilter .= "type IN (" . implode(',', $parts) . ")";
        }

        elseif ($filters['event_type']) {
            $sqlFilter = 'workshop.event_type = :event_type';
            $values[':event_type'] = $filters['type'];
        }

        if ($filters['excluded']) {
            $sqlFilter .=' AND workshop.id != :excluded';
            $values[':excluded'] = $filters['excluded'];
        }

        if($sqlFilter) {
            $sqlFilter = 'WHERE ' . $sqlFilter;
            $order='ORDER BY date_in ASC';
        } else {
            $sqlFilter = '';
            $order='ORDER BY id ASC';
        }

        if(self::default_lang($lang) === Config::get('lang')) {
            $different_select=" IFNULL(workshop_lang.title, workshop.title) as title,
                                IFNULL(workshop_lang.subtitle, workshop.subtitle) as subtitle,
                                IFNULL(workshop_lang.description, workshop.description) as description";
        }
        else {
            $different_select=" IFNULL(workshop_lang.title, IFNULL(eng.title,workshop.title)) as title,
                                IFNULL(workshop_lang.subtitle, IFNULL(eng.subtitle,workshop.subtitle)) as subtitle,
                                IFNULL(workshop_lang.description, IFNULL(eng.description,workshop.description)) as description";
            $eng_join=" LEFT JOIN workshop_lang as eng
                            ON  eng.id = workshop.id
                            AND eng.lang = 'en'";
        }

        $values[':lang']=$lang;

        $sql = "SELECT
                    workshop.id,
                    workshop.online,
                    workshop.date_in,
                    workshop.date_out,
                    workshop.schedule,
                    workshop.url,
                    workshop.call_id,
                    workshop.venue,
                    workshop.city,
                    workshop.venue_address,
                    workshop.header_image,
                    workshop.workshop_location,
                    $different_select
                FROM workshop
                LEFT JOIN workshop_lang
                    ON  workshop_lang.id = workshop.id
                    AND workshop_lang.lang = :lang
                $eng_join
                $sqlJoin
                $sqlFilter
                $order
                ";

        $query = self::query($sql, $values);

        foreach ($query->fetchAll(PDO::FETCH_CLASS, __CLASS__) as $item) {
            $list[] = $item;
        }

        return $list;
    }

    /**
     * Workshops listing
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
        $sql = '';
        $sqlJoin = '';
        $other_fields = [];

        if (isset($filters['node'])) {
            $sqlJoin .= "INNER JOIN node_workshop ON node_workshop.workshop_id = workshop.id and node_workshop.node_id = :node ";
            $values[":node"] = $filters['node'];
        }

        if($count) {
            $sql = "SELECT COUNT(id) FROM workshop$sql";
            return (int) self::query($sql, $values)->fetchColumn();
        }

        $offset = (int) $offset;
        $limit = (int) $limit;

        if(!$lang) $lang = Lang::current();
        $values['viewLang'] = $lang;
        list($fields, $joins) = self::getLangsSQLJoins($lang);

        $other_fields = implode(",\n", $other_fields);
        if ($other_fields) $other_fields .= ',';

        $sql ="SELECT
                workshop.id,
                workshop.online,
                workshop.title,
                $fields,
                workshop.subtitle,
                workshop.description,
                workshop.date_in,
                workshop.date_out,
                workshop.schedule,
                workshop.url,
                workshop.call_id,
                workshop.venue,
                workshop.city,
                workshop.venue_address,
                workshop.header_image,
                workshop.workshop_location,
                $other_fields
                :viewLang as viewLang

            FROM workshop
            $joins
            $sqlJoin
            $sql
            ORDER BY `id` DESC
            LIMIT $offset,$limit";

        if($query = self::query($sql, $values)) {
            return $query->fetchAll(PDO::FETCH_CLASS, __CLASS__);
        }

        return [];
    }

    public function getSpheres () {
        if($this->spheresList) return $this->spheresList;
        $values = [':workshop' => $this->id];

        $lang = Lang::current();
        list($fields, $joins) = Sphere::getLangsSQLJoins($lang, Config::get('lang'));

        $sql = "SELECT
                sphere.id,
                sphere.icon,
                $fields
            FROM workshop_sphere
            INNER JOIN sphere ON sphere.id = workshop_sphere.sphere_id
            $joins
            WHERE workshop_sphere.workshop_id = :workshop
            ORDER BY workshop_sphere.order ASC";
        $query = static::query($sql, $values);
        $this->spheresList = $query->fetchAll(PDO::FETCH_CLASS, 'Goteo\Model\Sphere');

        return $this->spheresList;
    }

    public function getStories () {
       if($this->storiesList) return $this->storiesList;
        $values = [':workshop' => $this->id];

        $lang = Lang::current();
        list($fields, $joins) = Stories::getLangsSQLJoins($lang, Config::get('lang'));

        $sql = "SELECT
                stories.id,
                stories.image,
                $fields
            FROM workshop_stories
            INNER JOIN stories ON stories.id = workshop_stories.stories_id
            $joins
            WHERE workshop_stories.workshop_id = :workshop
            ORDER BY workshop_stories.order ASC";
        $query = static::query($sql, $values);
        $this->storiesList = $query->fetchAll(PDO::FETCH_CLASS, 'Goteo\Model\Stories');

        return $this->storiesList;
    }

    /**
     *  Posts of this workshop
     */
    public function getPosts () {
       if($this->postsList) return $this->postsList;

        $this->postsList = GeneralPost::getList(['workshop' => $this->id ], true, 0, $limit = 3, false);

        return $this->postsList;
    }

    public function getSponsors($type=WorkshopSponsor::TYPE_SIDE) {
        if($this->spheresList) return $this->spheresList;
        $values = [':workshop' => $this->id, ':type' => $type];

        $sql = "SELECT
                workshop_sponsor.*
            FROM workshop_sponsor

            WHERE workshop_sponsor.workshop = :workshop AND workshop_sponsor.type= :type
            ORDER BY workshop_sponsor.order ASC";
        $query = static::query($sql, $values);
        $this->sponsorsList = $query->fetchAll(PDO::FETCH_CLASS, 'Goteo\Model\Workshop\WorkshopSponsor');

        return $this->sponsorsList;
    }

    public static function getListEventTypes(){
        return Config::get('workshop.event_types');
    }

    public function getHeaderImage() {
        if(!$this->HeaderImageInstance instanceOf Image) {
            $this->HeaderImageInstance = new Image($this->header_image);
        }
        return $this->HeaderImageInstance;
    }

    // returns the current project
    public function getCall() {
        if(isset($this->callObject)) return $this->callObject;
        try {
            $this->callObject = Call::get($this->call_id);
        } catch(ModelNotFoundException $e) {
            $this->callObject = false;
        }
        return $this->callObject;
    }

    public function expired() {
        $date=new Datetime($this->date_in);
        $date_now=new DateTime("now");

        return $date<=$date_now;
    }

    // returns the current location
    public function getLocation() {
        if($this->locationObject) return $this->locationObject;
        $this->locationObject = WorkshopLocation::get($this);
        return $this->locationObject;
    }


    /**
     * @param array $errors
     * @return bool
     */
    public function save(&$errors = array()) {

        if (!$this->validate($errors))
            return false;

        // TODO: handle uploaded files here?
        // If instanceOf Image, means already uploaded (via API probably), just get the name
        if($this->header_image instanceOf Image)
            $this->header_image = $this->header_image->getName();

        $fields = array(
            'id',
            'title',
            'lang',
            'subtitle',
            'online',
            'blockquote',
            'event_type',
            'description',
            'url',
            'date_in',
            'date_out',
            'schedule',
            'header_image',
            'venue',
            'city',
            'venue_address',
            'how_to_get',
            'map_iframe',
            'workshop_location',
            'schedule_file_url',
            'terms_file_url'
        );

        if($this->call_id) $fields[] = 'call_id';

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Workshop save error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * @param array $errors
     * @return bool
     */
    public function validate(&$errors = array()) {
        if(empty($this->title)) {
            $errors[] = "Emtpy title";
        }
        return empty($errors);
    }
}
