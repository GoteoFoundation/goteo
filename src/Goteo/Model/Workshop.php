<?php

/*
* Model for tax workshop
*/

namespace Goteo\Model;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Model\Workshop\WorkshopSponsor;
use Goteo\Model\Workshop\WorkshopLocation;
use Goteo\Model\Blog\Post as GeneralPost;


class Workshop extends \Goteo\Core\Model {

    public
    $id,
    $title,
    $subtitle,
    $online,
    $blockquote,
    $type,
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
    $modified;


    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);

        if(empty($this->lang)) $this->lang = Config::get('sql_lang');
    }

    public static function getLangFields() {
        return ['title', 'subtitle', 'blockquote', 'description', 'schedule'];
    }

    /**
     * Get data about a workshop
     *
     * @param   int    $id         workshop id.
     * @return  Workshop object
     */
    static public function get($id, $lang = null) {

        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql="SELECT
                    workshop.id,
                    workshop.online,
                    workshop.date_in,
                    workshop.date_out,
                    $fields,
                    workshop.url,
                    workshop.call_id,
                    workshop.venue,
                    workshop.city,
                    workshop.venue_address,
                    workshop.header_image,
                    workshop.map_iframe
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
     * Workshop list
     *
     * @param  array  $filters
     * @return mixed            Array of workshops
     */
    public static function getAll($filters = array()) {

        $lang = Lang::current();

        $values = array();

        $list = array();

        if ($filters['call']) {
            $sqlFilter = 'workshop.call_id = :call';
            $values[':call'] = $filters['call'];
        }

        if (is_array($filters['type'])) {
            $parts = [];
            foreach($filters['type'] as $i => $type) {
                    $parts[] = ':type' . $i;
                    $values[':type' . $i] = $type;
            }
            if($parts) $sqlFilter .= "type IN (" . implode(',', $parts) . ")";
        }

        elseif ($filters['type']) {
            $sqlFilter = 'workshop.type = :type';
            $values[':type'] = $filters['type'];
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
                    $different_select
                FROM workshop
                LEFT JOIN workshop_lang
                    ON  workshop_lang.id = workshop.id
                    AND workshop_lang.lang = :lang
                $eng_join
                $sqlFilter
                $order
                ";

        //die(\sqldbg($sql, $values));

        $query = self::query($sql, $values);

        foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
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
        $sqlFilters = [];
        $sql = '';

        if($count) {
            // Return count
            $sql = "SELECT COUNT(id) FROM workshop$sql";
            // echo \sqldbg($sql, $values);
            return (int) self::query($sql, $values)->fetchColumn();
        }

        $offset = (int) $offset;
        $limit = (int) $limit;

        if(!$lang) $lang = Lang::current();
        $values['viewLang'] = $lang;
        list($fields, $joins) = self::getLangsSQLJoins($lang);

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
                :viewLang as viewLang

            FROM workshop
            $joins
            $sql
            ORDER BY `id` DESC
            LIMIT $offset,$limit";

        // print_r($values);die(\sqldbg($sql, $values));
        if($query = self::query($sql, $values)) {
            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }
        return [];
    }


    /**
     *  Spheres of this workshop
     */
    public function getSpheres () {
        if($this->spheresList) return $this->spheresList;
        $values = [':workshop' => $this->id];

        list($fields, $joins) = Sphere::getLangsSQLJoins($this->viewLang, Config::get('sql_lang'));

        $sql = "SELECT
                sphere.id,
                sphere.icon,
                $fields
            FROM workshop_sphere
            INNER JOIN sphere ON sphere.id = workshop_sphere.sphere_id
            $joins
            WHERE workshop_sphere.workshop_id = :workshop
            ORDER BY workshop_sphere.order ASC";
        // die(\sqldbg($sql, $values));
        $query = static::query($sql, $values);
        $this->spheresList = $query->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\Sphere');
        return $this->spheresList;

    }

    /**
     *  Stories of this workshop
     */
    public function getStories () {
       if($this->storiesList) return $this->storiesList;
        $values = [':workshop' => $this->id];

        $lang = Lang::current();

        list($fields, $joins) = Stories::getLangsSQLJoins($this->viewLang, $lang);

        $sql = "SELECT
                stories.id,
                stories.image,
                $fields
            FROM workshop_stories
            INNER JOIN stories ON stories.id = workshop_stories.stories_id
            $joins
            WHERE workshop_stories.workshop_id = :workshop
            ORDER BY workshop_stories.order ASC";
        // die(\sqldbg($sql, $values));
        $query = static::query($sql, $values);
        $this->storiesList = $query->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\Stories');
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

    /**
     *  Sponsors of this workshop
     */
    public function getSponsors () {
        if($this->spheresList) return $this->spheresList;
        $values = [':workshop' => $this->id];

        $sql = "SELECT
                workshop_sponsor.*
            FROM workshop_sponsor

            WHERE workshop_sponsor.workshop = :workshop
            ORDER BY workshop_sponsor.order ASC";
         //die(\sqldbg($sql, $values));
        $query = static::query($sql, $values);
        $this->sponsorsList = $query->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\Workshop\WorkshopSponsor');
        return $this->sponsorsList;

    }

    /*
     *  List of types
     */
    public static function getListTypes(){
        return Config::get('workshop.types');
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
        $date=new \Datetime($this->date_in);
        $date_now=new \DateTime("now"); 

        return $date<=$date_now;
    }

    /**
     * Save.
     *
     * @param   type array  $errors
     * @return  type bool   true|false
     */
    public function save(&$errors = array()) {

        if (!$this->validate($errors))
            return false;

        // Dropfiles type always return an array, just get the first element if required
        if($this->header_image && is_array($this->header_image)) {
            $this->header_image = $this->header_image[0];
        } else {
            $this->header_image = null;
        }

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
            'type',
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
            'schedule_file_url'
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
     * Validate.
     *
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
    public function validate(&$errors = array()) {
        if(empty($this->title)) {
            $errors[] = "Emtpy title";
        }
        return empty($errors);
    }


}


