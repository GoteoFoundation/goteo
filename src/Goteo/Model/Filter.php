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

use Goteo\Core\Model;
use Goteo\Library\Text;
use Goteo\Application\Message;
use Goteo\Model\User;
use Goteo\Model\User\UserLocation;
use Goteo\Model\User\DonorLocation;
use Goteo\Model\Filter\FilterLocation;
use Goteo\Application\Exception\ModelNotFoundException;
use DateTime;
use Goteo\Util\Filter\FilterDonor;
use PDO;
use PDOException;

class Filter extends Model {

    const USER = "user";
    const DONOR = "donor";
    const NODONOR = "no-donor";
    const PROMOTER = "promoter";
    const MATCHER = "matcher";
    const TEST = "test";
    const UNIQUE = "unique";
    const MULTIDONOR = "multidonor";
    const LAST_WEEK = 0;
    const LAST_MONTH = 1;
    const LAST_YEAR = 2;
    const FROM_NEW_YEAR = 3;
    const PREVIOUS_YEAR = 4;
    const TWO_YEARS_AGO = 5;

    public
        $id,
        $name,
        $description,
        $cert,
        $role,
        $startdate,
        $enddate,
        $project_status,
        $invest_status,
        $amount,
        $donor_status,
        $typeofdonor,
        $foundationdonor,
        $wallet,
        $filter_location,
        $projects = [],
        $calls = [],
        $channels = [],
        $matchers = [],
        $sdgs = [],
        $footprints = [],
        $social_commitments = [],
        $forced;

    static public function get($id): Filter {
        $query = static::query('SELECT * FROM filter WHERE id = ?', $id);
        $filter = $query->fetchObject(__CLASS__);

        if (!$filter instanceof Filter) {
            throw new ModelNotFoundException("[$id] not found");
        }

        $filter->projects = self::getFilterProject($id);
        $filter->calls = self::getFilterCall($id);
        $filter->channels = self::getFilterNode($id);
        $filter->matchers = self::getFilterMatcher($id);
        $filter->sdgs = self::getFilterSDG($id);
        $filter->footprints = self::getFilterFootprint($id);
        $filter->social_commitments = self::getFilterSocialCommitments($id);

        return $filter;
    }

    static public function getAll() {
        $query = static::query('SELECT * FROM filter');
        $filters = $query->fetchAll(PDO::FETCH_CLASS, __CLASS__);
        return $filters;
    }

    public static function getList ($filters = array(), $offset = 0, $limit = 0, $count = false) {

        $sqlWhere = "";

        if ($count) {
            $sql = "SELECT COUNT(filter.id)
            FROM filter
            $sqlWhere";
            return (int) self::query($sql)->fetchColumn();
        }

        $sql = "SELECT *
                FROM filter
                $sqlWhere
                LIMIT $offset, $limit
            ";

        $query = static::query($sql);
        return $query->fetchAll(PDO::FETCH_CLASS, __CLASS__);
    }

    public function getFilterLocation(): FilterLocation {
        return FilterLocation::get($id);
    }

    static public function getFilterProject ($filter){
        $query = static::query('SELECT `project` FROM filter_project WHERE filter = ?', $filter);
        $projects = $query->fetchAll(PDO::FETCH_ASSOC);

        $filter_projects = [];

        foreach($projects as $project) {
            foreach($project as $value) {
                $project = Project::getMini($value);
                $filter_projects[$value] = $project->name;
            }
        }

        return $filter_projects;
    }

    static public function getFilterCall ($filter){
        $query = static::query('SELECT `call` FROM filter_call WHERE filter = ?', $filter);
        $calls = $query->fetchAll(PDO::FETCH_ASSOC);

        $filter_calls = [];

        foreach($calls as $call) {
            foreach($call as $key => $value) {
                $call = Call::getMini($value);
                $filter_calls[$value] = $call->name;
            }
        }

        return $filter_calls;
    }

    static public function getFilterNode ($filter){
        $query = static::query('SELECT `node` FROM filter_node WHERE filter = ?', $filter);
        $nodes = $query->fetchAll(PDO::FETCH_ASSOC);

        $filter_nodes = [];

        foreach($nodes as $node) {
            foreach($node as $value) {
                $node = Node::getMini($value);
                $filter_nodes[$value] = $node->name;
            }
        }

        return $filter_nodes;
    }

    static public function getFilterMatcher ($filter){
        $query = static::query('SELECT `matcher` FROM filter_matcher WHERE filter = ?', $filter);
        $matchers = $query->fetchAll(PDO::FETCH_ASSOC);

        $filter_matchers = [];

        foreach($matchers as $matcher) {
            foreach($matcher as $value) {
                $matcher = Matcher::get($value);
                $filter_matchers[$value] = $matcher->name;
            }
        }

        return $filter_matchers;
    }

    static public function getFilterSDG ($filter){
        $query = static::query('SELECT `sdg` FROM filter_sdg WHERE filter = ?', $filter);
        $sdgs = $query->fetchAll(PDO::FETCH_ASSOC);

        $filter_sdgs = [];

        foreach($sdgs as $sdg) {
            foreach($sdg as $key => $value) {
                $sdg = Sdg::get($value);
                $filter_sdgs[$value] = $sdg->name;
            }
        }

        return $filter_sdgs;
    }

    static public function getFilterFootprint ($filter){
        $query = static::query('SELECT `footprint` FROM filter_footprint WHERE filter = ?', $filter);
        $footprints = $query->fetchAll(PDO::FETCH_ASSOC);

        $filter_footprints = [];

        foreach($footprints as $footprint) {
            foreach($footprint as $value) {
                $footprint = Footprint::get($value);
                $filter_footprints[$value] = $footprint->name;
            }
        }

        return $filter_footprints;
    }

    /**
     * @return SocialCommitment[]
     */
    static public function getFilterSocialCommitments(int $filter): array
    {
        $query = static::query('SELECT `social_commitment` FROM filter_socialcommitment WHERE filter = ?', $filter);
        $socialCommitments = $query->fetchAll(PDO::FETCH_ASSOC);

        $filterSocialCommitments = [];

        foreach($socialCommitments as $socialCommitment) {
            foreach($socialCommitment as $value) {
                $socialCommitment = SocialCommitment::get($value);
                $filterSocialCommitments[$value] = $socialCommitment->name;
            }
        }

        return $filterSocialCommitments;
    }

    public function setFilterProjects(){
        $values = Array(':filter' => $this->id, ':project' => '');

        try {
            $query = static::query('DELETE FROM filter_project WHERE filter = :filter', Array(':filter' => $this->id));
        }
        catch (PDOException $e) {
            Message::error("Error deleting previous filter projects for filter " . $this->id . " " . $e->getMessage());
        }

        foreach($this->projects as $value) {
            $values[':project'] = $value;
            try {
                $query = static::query('INSERT INTO filter_project(`filter`, `project`) VALUES(:filter,:project)', $values);
            }
            catch (PDOException $e) {
                Message::error("Error saving filter projects " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    public function setFilterCalls(){
        $values = Array(':filter' => $this->id, ':call' => '');

        try {
            $query = static::query('DELETE FROM filter_call WHERE filter = :filter', Array(':filter' => $this->id));
        }
        catch (PDOException $e) {
            Message::error("Error deleting previous filter calls for filter " . $this->id . " " . $e->getMessage());
        }

        foreach($this->calls as $value) {
            $values[':call'] = $value;
            try {
                $query = static::query('INSERT INTO filter_call(`filter`, `call`) VALUES(:filter,:call)', $values);
            }
            catch (PDOException $e) {
                Message::error("Error saving filter call " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    public function setFilterNodes(){
        $values = Array(':filter' => $this->id, ':node' => '');

        try {
            $query = static::query('DELETE FROM filter_node WHERE filter = :filter', Array(':filter' => $this->id));
        }
        catch (PDOException $e) {
            Message::error("Error deleting previous filter nodes for filter " . $this->id . " " . $e->getMessage());
        }

        foreach($this->channels as $value) {
            $values[':node'] = $value;
            try {
                $query = static::query('INSERT INTO filter_node(`filter`, `node`) VALUES(:filter,:node)', $values);
            }
            catch (PDOException $e) {
                Message::error("Error saving filter node " . $e->getMessage());
                return false;
            }
        }
        return true;
    }


    public function setFilterMatcher(){
        $values = Array(':filter' => $this->id, ':matcher' => '');

        try {
            $query = static::query('DELETE FROM filter_matcher WHERE filter = :filter', Array(':filter' => $this->id));
        }
        catch (PDOException $e) {
            Message::error("Error deleting previous filter matcher for filter " . $this->id . " " . $e->getMessage());
        }

        foreach($this->matchers as $value) {
            $values[':matcher'] = $value;
            try {
                $query = static::query('INSERT INTO filter_matcher(`filter`, `matcher`) VALUES(:filter,:matcher)', $values);
            }
            catch (PDOException $e) {
                Message::error("Error saving filter matcher " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    public function setFilterSDG(){
        $values = Array(':filter' => $this->id, ':sdg' => '');

        try {
            $query = static::query('DELETE FROM filter_sdg WHERE filter = :filter', Array(':filter' => $this->id));
        }
        catch (PDOException $e) {
            Message::error("Error deleting previous filter sdg for filter " . $this->id . " " . $e->getMessage());
        }

        foreach($this->sdgs as $value) {
            $values[':sdg'] = $value;
            try {
                $query = static::query('INSERT INTO filter_sdg(`filter`, `sdg`) VALUES(:filter,:sdg)', $values);
            }
            catch (PDOException $e) {
                Message::error("Error saving filter sdg " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    public function setFilterFootprint(){
        $values = Array(':filter' => $this->id, ':footprint' => '');

        try {
            $query = static::query('DELETE FROM filter_footprint WHERE filter = :filter', Array(':filter' => $this->id));
        }
        catch (PDOException $e) {
            Message::error("Error deleting previous filter footprint for filter " . $this->id . " " . $e->getMessage());
        }

        foreach($this->footprints as $value) {
            $values[':footprint'] = $value;
            try {
                $query = static::query('INSERT INTO filter_footprint(`filter`, `footprint`) VALUES(:filter,:footprint)', $values);
            }
            catch (PDOException $e) {
                Message::error("Error saving filter footprint " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    public function setFilterSocialCommitment(): bool
    {
        $values = [':filter' => $this->id, ':social_commitment' => ''];

        try {
            static::query('DELETE FROM filter_socialcommitment WHERE filter = :filter', Array(':filter' => $this->id));
        }
        catch (PDOException $e) {
            Message::error("Error deleting previous filter footprint for filter " . $this->id . " " . $e->getMessage());
        }


        foreach($this->social_commitments as $value) {

            $values[':social_commitment'] = $value;
            try {
                static::query('INSERT INTO filter_socialcommitment(`filter`, `social_commitment`) VALUES(:filter,:social_commitment)', $values);
            }
            catch (PDOException $e) {
                Message::error("Error saving filter footprint " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    public function validate(&$errors = array()) {

        if (empty($this->name))
            $errors['name'] = Text::get('filter-without-name');
        if (empty($this->description))
            $errors['description'] = Text::get('filter-without-description');
        if (empty($this->role))
            $errors['role'] = Text::get('filter-without-role');
        return empty($errors);
    }

    public function save (&$errors = array()) {

        if(!$this->validate($errors)) return false;

        $fields = array(
            'id',
            'name',
            'description',
            'cert',
            'role',
            'startdate',
            'enddate',
            'project_status',
            'invest_status',
            'amount',
            'donor_status',
            'typeofdonor',
            'foundationdonor',
            'wallet',
            'filter_location',
            'forced'
        );

        try {
            if($this->filter_location instanceOf FilterLocation) {
                $this->filter_location->id = $this->id;
                if($this->filter_location->save($errors)) {
                    $this->filter_location = $this->filter_location->location ?: $this->filter_location->name;
                } else {
                    unset($this->filter_location);
                }
            }

            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);
            // return true;

            $this->setFilterProjects();
            $this->setFilterCalls();
            $this->setFilterNodes();
            $this->setFilterMatcher();
            $this->setFilterSDG();
            $this->setFilterFootprint();
            $this->setFilterSocialCommitment();
        } catch(PDOException $e) {
            print("exception");
            $errors[] = "Error updating filter " . $e->getMessage();
            return false;
        }

        return true;
    }

    public function isUsed() {

        $constraints = self::dbReferencialConstraints(['delete_rule' => 'RESTRICT']);
        $sql = "SELECT filter.id FROM filter ";
        $values = [];

        foreach($constraints as $i => $constraint) {
            $sql .= "INNER JOIN ". $constraint['TABLE_NAME'] . " as " . $constraint['TABLE_NAME'] . "_" . $i .
                    " ON filter.id = ". $constraint['TABLE_NAME'] . "_" . $i . ".filter ";
        }
        $sql .= "WHERE filter.id = :id";
        $values[':id'] = $this->id;
        $query = $this->query($sql, $values);

        return (!empty($query->fetch()));
    }

    public function getUsers($offset = 0, $limit = 0, $count = false, $lang = null) {
        $receivers = array();

        $values = array();
        $sqlFields  = '';
        $sqlInner  = '';
        $sqlFilter = '';

        if ($this->filter_location) {
            $loc = FilterLocation::get($this->id);
            $loc = new UserLocation($loc);
            $loc->location = $this->donor_location;
            $distance = $loc->radius ? $loc->radius : 50; // search in 50 km by default

            // $sqlInner .= " INNER JOIN user
            // ON user.user = user.id ";

            $sqlInner .= " INNER JOIN user_location
                            ON user_location.id = user.id ";
            $location_parts = UserLocation::getSQLFilterParts($loc, $distance, true, $loc->city, 'user.location');
            $values[":location_minLat"] = $location_parts['params'][':location_minLat'];
            $values[":location_minLon"] = $location_parts['params'][':location_minLon'];
            $values[":location_maxLat"] = $location_parts['params'][':location_maxLat'];
            $values[":location_maxLon"] = $location_parts['params'][':location_maxLon'];
            $values[":location_text"] = $location_parts['params'][':location_text'];
            $sqlFilter .= " AND ({$location_parts['firstcut_where']})" ;
        }

        if (isset($lang)) {
            $parts = [];
            $sqlFilter .= " AND user.lang ";
            foreach($lang as $key => $value) {
                $parts[] = ':lang' . $key;
                $values[':lang' . $key] = $value;
            }
            if($parts) $sqlFilter .= " IN (" . implode(',', $parts) . ")";
        }

        $sqlFilter = ($this->forced) ? $sqlFilter : " AND (user_prefer.mailing = 0 OR user_prefer.`mailing` IS NULL) " . $sqlFilter;
        if($count) {
            $sql = "SELECT COUNT(DISTINCT(user.id))
                    FROM user
                    LEFT JOIN user_prefer
                        ON user.id = user_prefer.user
                    $sqlInner
                    WHERE user.active = 1 $sqlFilter";
            return (int) User::query($sql, $values)->fetchColumn();
        }

        $sql = "SELECT
                    user.id as user,
                    user.name as name,
                    user.email as email
                    $sqlFields
                FROM user
                LEFT JOIN user_prefer
                    ON user.id = user_prefer.user
                $sqlInner
                WHERE user.active = 1 $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC
                ";

        if ($limit) $sql .= "LIMIT $offset, $limit ";

        if ($query = User::query($sql, $values)) {
            $receivers = $query->fetchAll(PDO::FETCH_CLASS, 'Goteo\Model\User');
        }

        return $receivers;
    }

    public function getUsersSQL($lang = null, $prefix = '') {

        $values = array();
        $sqlFields  = '';
        $sqlInner  = '';
        $sqlFilter = '';

        if ($this->filter_location) {
            $loc = FilterLocation::get($this->id);
            $loc = new UserLocation($loc);
            $loc->location = $this->donor_location;
            $distance = $loc->radius ?: 50; // search in 50 km by default

            // $sqlInner .= " INNER JOIN user
            // ON user.user = user.id ";

            $sqlInner .= " INNER JOIN user_location
                            ON user_location.id = user.id ";
            $location_parts = UserLocation::getSQLFilterParts($loc, $distance, true, $loc->city, 'user.location');
            $values[":location_minLat"] = $location_parts['params'][':location_minLat'];
            $values[":location_minLon"] = $location_parts['params'][':location_minLon'];
            $values[":location_maxLat"] = $location_parts['params'][':location_maxLat'];
            $values[":location_maxLon"] = $location_parts['params'][':location_maxLon'];
            $values[":location_text"] = $location_parts['params'][':location_text'];
            $sqlFilter .= " AND ({$location_parts['firstcut_where']})" ;
        }

        if (isset($lang)) {
            $parts = [];
            $sqlFilter .= " AND user.lang ";
            foreach($lang as $key => $value) {
                $parts[] = ':lang' . $key;
                $values[':lang' . $key] = $value;
            }
            if($parts) $sqlFilter .= " IN (" . implode(',', $parts) . ")";
        }

        $values[':prefix'] = $prefix;

        $sqlFilter = ($this->forced) ? $sqlFilter : " AND (user_prefer.mailing = 0 OR user_prefer.`mailing` IS NULL) " . $sqlFilter;
        $sql = "SELECT
                    :prefix,
                    user.id as user,
                    user.name as name,
                    user.email as email
                    $sqlFields
                FROM user
                LEFT JOIN user_prefer
                    ON user.id = user_prefer.user
                $sqlInner
                WHERE user.active = 1
                $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC
                ";

        return [$sql, $values];
    }

    public function getDonors($offset = 0, $limit = 0, $count = false, $lang = null) {
        $filterUtil = new FilterDonor($this);

        if ($count)
            return $this->getDonorsCount($lang);

        return $filterUtil->getDonors($offset, $limit, $lang);
    }

    public function getDonorsCount(?string $lang = null): int
    {
        $filterUtil = new FilterDonor($this);

        return $filterUtil->calculate($lang);
    }

    public function getDonorsSQL($lang = null, $prefix = '') {
        $filterUtil = new FilterDonor($this);

        return $filterUtil->getSqlFilter($lang, $prefix);

    }

    public function getNoDonors($offset = 0, $limit = 0, $count = false, $lang = null) {

        $receivers = array();
        $values = array();
        $sqlInner  = '';
        $sqlFilter = '';
        $investStatus = Invest::$RAISED_STATUSES_AND_DONATED;

        if ($this->invest_status) {
            $investStatus = [$this->invest_status];
        }

        $sqlFilter .= " AND user.id NOT IN (
            SELECT invest.user
            FROM invest
            WHERE invest.status IN ";

        $parts = [];
        foreach($investStatus as $index => $status) {
            $parts[] = ':invest_status' . $index;
            $values[':invest_status' . $index] = $status;
        }
        $sqlFilter .= " (" . implode(',', $parts) . ") ";

        if (isset($this->startdate)) {
            $sqlFilter .= " AND invest.invested BETWEEN :startdate ";
            $values[':startdate'] = $this->startdate;

            if(isset($this->enddate)) {
                $sqlFilter .= " AND :enddate ";
                $values[':enddate'] = $this->enddate;
            } else {
                $sqlFilter .= " AND curdate() ";
            }
        } else if (isset($this->enddate)) {
            $sqlFilter .= " AND invest.invested < :enddate ";
            $values[':enddate'] = $this->enddate;
        }

        $sqlFilter .= "GROUP BY invest.user )";

        if (isset($this->startdate)) {
            $sqlFilter .= " AND user.id IN (
                SELECT invest.user
                FROM invest
                WHERE invest.status IN ";

            $parts = [];
            foreach([Invest::STATUS_CHARGED, Invest::STATUS_PAID, Invest::STATUS_RETURNED, Invest::STATUS_TO_POOL] as $index => $status) {
                    $parts[] = ':invest_status' . $index;
                    $values[':invest_status' . $index] = $status;
                }
            $sqlFilter .= " (" . implode(',', $parts) . ") ";


            $sqlFilter .= " AND invest.invested < :startdate ";
            $values[':startdate'] = $this->startdate;

            if(isset($this->enddate)) {
                $sqlFilter .= " OR invest.invested > :enddate ";
                $values[':enddate'] = $this->enddate;
            }
            $sqlFilter .= "GROUP BY invest.user )";

        } else if (isset($this->enddate)) {
            $sqlFilter .= " AND user.id IN (
                SELECT invest.user
                FROM invest
                WHERE invest.status IN ";

            $parts = [];
            foreach([Invest::STATUS_CHARGED, Invest::STATUS_PAID, Invest::STATUS_RETURNED, Invest::STATUS_TO_POOL] as $index => $status) {
                    $parts[] = ':invest_status' . $index;
                    $values[':invest_status' . $index] = $status;
                }
            $sqlFilter .= " (" . implode(',', $parts) . ") ";

            $sqlFilter .= "AND invest.invested > :enddate ";
            $values[':enddate'] = $this->enddate;
            $sqlFilter .= "GROUP BY invest.user )";
        }

        if (isset($lang)) {
            $parts = [];
            $sqlFilter .= " AND user.lang ";
            foreach($lang as $key => $value) {
                $parts[] = ':lang' . $key;
                $values[':lang' . $key] = $value;
            }
            if($parts) $sqlFilter .= " IN (" . implode(',', $parts) . ") ";
        }

        if (isset($this->wallet)) {
            $sqlFilter .= " AND user.id ";
            $sqlFilter .= ($this->wallet)? "IN " : "NOT IN ";
            $sqlFilter .= " ( SELECT user_pool.user
                              FROM user_pool
                              WHERE user_pool.amount > 0 )";
        }

        if (isset($this->foundationdonor)) {
            $sqlFilter .= " AND user.id ";
            $sqlFilter .= ($this->foundationdonor)? "" : "NOT ";
            $sqlFilter .= " IN (
                SELECT i.`user`
                FROM invest i
                WHERE
                i.status= :status_donated
                )";
            $values[':status_donated'] = Invest::STATUS_DONATED;
        }

        if ($this->filter_location) {
            $loc = FilterLocation::get($this->id);
            $loc = new DonorLocation($loc);
            $loc->location = $this->donor_location;
            $distance = $loc->radius ?: 50; // search in 50 km by default

            $sqlInner .= " INNER JOIN donor
            ON donor.user = user.id ";

            $sqlInner .= " INNER JOIN donor_location
                            ON donor_location.id = donor.id ";
            $location_parts = DonorLocation::getSQLFilterParts($loc, $distance, true, $loc->city, 'donor.location');
            $values[":location_minLat"] = $location_parts['params'][':location_minLat'];
            $values[":location_minLon"] = $location_parts['params'][':location_minLon'];
            $values[":location_maxLat"] = $location_parts['params'][':location_maxLat'];
            $values[":location_maxLon"] = $location_parts['params'][':location_maxLon'];
            $values[":location_text"] = $location_parts['params'][':location_text'];
            $sqlFilter .= " AND ({$location_parts['firstcut_where']})" ;
        }

        $sqlFilter = ($this->forced) ? $sqlFilter : " AND (user_prefer.mailing = 0 OR user_prefer.`mailing` IS NULL) " . $sqlFilter;
        if ($count) {
            $sql = "SELECT COUNT(user.id)
                    FROM user
                    LEFT JOIN user_prefer
                    ON user_prefer.user = user.id
                    $sqlInner
                    WHERE user.active
                    $sqlFilter";
            return (int) User::query($sql, $values)->fetchColumn();
        }

        $sql = "SELECT
                    user.id as user,
                    user.name as name,
                    user.email as email
                FROM user
                $sqlInner
                WHERE user.active
                LEFT JOIN user_prefer
                ON user.id = user_prefer.user
                $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC
                ";

        if ($limit) $sql .= "LIMIT $count, $limit ";

         if ($query = User::query($sql, $values)) {
            $receivers = $query->fetchAll(PDO::FETCH_CLASS, 'Goteo\Model\User');
        }

        return $receivers;
    }

    public function getNoDonorsSQL($lang = null, $prefix = '') {

        $values = array();
        $sqlInner  = '';
        $sqlFilter = '';
        $values[':prefix'] = $prefix;
        $investStatus = Invest::$RAISED_STATUSES_AND_DONATED;

        if ($this->invest_status) {
            $investStatus = [$this->invest_status];
        }

        $sqlFilter .= " AND user.id NOT IN (
            SELECT invest.user
            FROM invest
            WHERE invest.status IN ";

        $parts = [];
        foreach($investStatus as $index => $status) {
                $parts[] = ':invest_status' . $index;
                $values[':invest_status' . $index] = $status;
            }
        $sqlFilter .= " (" . implode(',', $parts) . ") ";

        if (isset($this->startdate)) {
            $sqlFilter .= " AND invest.invested BETWEEN :startdate ";
            $values[':startdate'] = $this->startdate;

            if(isset($this->enddate)) {
                $sqlFilter .= " AND :enddate ";
                $values[':enddate'] = $this->enddate;
            } else {
                $sqlFilter .= " AND curdate() ";
            }
        } else if (isset($this->enddate)) {
            $sqlFilter .= " AND invest.invested < :enddate ";
            $values[':enddate'] = $this->enddate;
        }

        $sqlFilter .= "GROUP BY invest.user )";

        if (isset($this->startdate)) {
            $sqlFilter .= " AND user.id IN (
                SELECT invest.user
                FROM invest
                WHERE invest.status IN ";

            $parts = [];
            foreach([Invest::STATUS_CHARGED, Invest::STATUS_PAID, Invest::STATUS_RETURNED, Invest::STATUS_TO_POOL] as $index => $status) {
                $parts[] = ':invest_status' . $index;
                $values[':invest_status' . $index] = $status;
            }
            $sqlFilter .= " (" . implode(',', $parts) . ") ";
            $sqlFilter .= " AND invest.invested < :startdate ";
            $values[':startdate'] = $this->startdate;

            if(isset($this->enddate)) {
                $sqlFilter .= " OR invest.invested > :enddate ";
                $values[':enddate'] = $this->enddate;
            }
            $sqlFilter .= "GROUP BY invest.user )";

        } else if (isset($this->enddate)) {
            $sqlFilter .= " AND user.id IN (
                SELECT invest.user
                FROM invest
                WHERE invest.status IN ";
            $parts = [];

            foreach([Invest::STATUS_CHARGED, Invest::STATUS_PAID, Invest::STATUS_RETURNED, Invest::STATUS_TO_POOL] as $index => $status) {
                $parts[] = ':invest_status' . $index;
                $values[':invest_status' . $index] = $status;
            }

            $sqlFilter .= " (" . implode(',', $parts) . ") ";
            $sqlFilter .= "AND invest.invested > :enddate ";
            $values[':enddate'] = $this->enddate;
            $sqlFilter .= "GROUP BY invest.user )";
        }

        if (isset($lang)) {
            $parts = [];
            $sqlFilter .= " AND user.lang ";
            foreach($lang as $key => $value) {
                $parts[] = ':lang' . $key;
                $values[':lang' . $key] = $value;
            }
            if($parts) $sqlFilter .= " IN (" . implode(',', $parts) . ") ";
        }

        if (isset($this->wallet)) {
            $sqlFilter .= " AND user.id ";
            $sqlFilter .= ($this->wallet)? "IN " : "NOT IN ";
            $sqlFilter .= " ( SELECT user_pool.user
                              FROM user_pool
                              WHERE user_pool.amount > 0 )";
        }

        if (isset($this->foundationdonor)) {
            $sqlFilter .= " AND user.id ";
            $sqlFilter .= ($this->foundationdonor)? "" : "NOT ";
            $sqlFilter .= " IN (
                SELECT i.`user`
                FROM invest i
                WHERE
                i.status= :status_donated
                )";
            $values[':status_donated'] = Invest::STATUS_DONATED;
        }

        if ($this->filter_location) {
            $loc = FilterLocation::get($this->id);
            $loc = new DonorLocation($loc);
            $loc->location = $this->donor_location;
            $distance = $loc->radius ?: 50; // search in 50 km by default

            $sqlInner .= " INNER JOIN donor
            ON donor.user = user.id ";
            $sqlInner .= " INNER JOIN donor_location
                            ON donor_location.id = donor.id ";
            $location_parts = DonorLocation::getSQLFilterParts($loc, $distance, true, $loc->city, 'donor.location');
            $values[":location_minLat"] = $location_parts['params'][':location_minLat'];
            $values[":location_minLon"] = $location_parts['params'][':location_minLon'];
            $values[":location_maxLat"] = $location_parts['params'][':location_maxLat'];
            $values[":location_maxLon"] = $location_parts['params'][':location_maxLon'];
            $values[":location_text"] = $location_parts['params'][':location_text'];
            $sqlFilter .= " AND ({$location_parts['firstcut_where']})" ;
        }

        $sqlFilter = ($this->forced) ? $sqlFilter : " AND (user_prefer.mailing = 0 OR user_prefer.`mailing` IS NULL) " . $sqlFilter;
        $sql = "SELECT
                    :prefix,
                    user.id as user,
                    user.name as name,
                    user.email as email
                FROM user
                LEFT JOIN user_prefer
                ON user_prefer.user = user.id
                $sqlInner
                WHERE user.active
                $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC
                ";

        return [$sql,$values];
    }

    public function getPromoters($offset = 0, $limit = 0, $count = false, $lang = null) {

        $receivers = array();
        $values = array();
        $sqlInner  = '';
        $sqlFilter = '';
        $sqlInner .= "INNER JOIN project
            ON project.owner = user.id
        ";

        if (isset($this->project_status) && $this->project_status > -1) {
            $sqlFilter .= "
                AND project.status = :project_status
                ";
            $values[':project_status'] = $this->project_status;
        } else if ($this->project_status == Project::STATUS_NEGOTIATION) {
            $sqlFilter .= "
                AND project.status = :project_status AND project.`id` NOT REGEXP '[0-9a-f]{32}'
            ";
            $values[':project_status'] = Project::STATUS_EDITING;
        }

        $this->projects = $this->getFilterProject($this->id);
        $this->calls = $this->getFilterCall($this->id);
        $this->channels = $this->getFilterNode($this->id);
        $this->matchers = $this->getFilterMatcher($this->id);

        if (!empty($this->projects)) {
            foreach(array_keys($this->projects) as $index => $id) {
                if ($index < 1) {
                    $sqlFilter .= " AND ( project.id =  :project_".$index;
                } else {
                    $sqlFilter .= " OR project.id = :project_".$index;
                }
                $values[':project_'.$index] = $id;
            }
            $sqlFilter .= ") ";
        }

        if (!empty($this->calls)) {
            $sqlInner .= "INNER JOIN call_project
                on call_project.project = project.id
            ";

            foreach(array_keys($this->calls) as $index => $id) {
                if ($index < 1) {
                    $sqlFilter .= " AND ( call_project.call =  :call_".$index;
                } else {
                    $sqlFilter .= " OR call_project.call = :call_".$index;
                }
                $values[':call_'.$index] = $id;
            }
            $sqlFilter .= ") ";
        }

        if (!empty($this->channels)) {
            $sqlInner .= "LEFT JOIN node_project
                on node_project.project_id = project.id
            ";

            foreach(array_keys($this->channels) as $index => $id) {
                $parts[] = ':nodes_' . $index;
                $values[':nodes_' . $index] = $id;
            }
            if($parts) $sqlFilter .= " AND ( node_project.node_id IN (" . implode(',', $parts) . ") OR  project.node IN (" . implode(',', $parts) . ") ) ";
        }

        if (!empty($this->matchers)) {
            if (!empty($this->matchers) && !empty($sqlInner)) {
                $sqlInner .= "INNER JOIN matcher_project
                    on matcher_project.project_id = project.id
                ";

                foreach(array_keys($this->matchers) as $index => $id) {
                    if ($index < 1) {
                        $sqlFilter .= " AND ( matcher_project.matcher_id =  :matchers_".$index;
                    } else {
                        $sqlFilter .= " OR matcher_project.matcher_id = :matchers_".$index;
                    }
                    $values[':matchers_'.$index] = $id;
                }
                $sqlFilter .= " ) ";
            }
        }

        if (isset($this->startdate)) {
            $sqlFilter .= " AND project.created BETWEEN :startdate";
            $values['startdate'] = $this->startdate;

            if(isset($this->enddate)) {
                $sqlFilter .= " AND :enddate";
                $values['enddate'] = $this->enddate;
            } else {
                $sqlFilter .= " AND curdate()";
            }
        } else if (isset($this->enddate)) {
            $sqlFilter .= " AND project.created < :enddate";
            $values['enddate'] = $this->enddate;
        }

        if (isset($lang)) {
            $parts = [];
            $sqlFilter .= " AND user.lang ";
            foreach($lang as $key => $value) {
                $parts[] = ':lang' . $key;
                $values[':lang' . $key] = $value;
            }
            if($parts) $sqlFilter .= " IN (" . implode(',', $parts) . ")";
        }

        if ($this->filter_location) {
            $loc = FilterLocation::get($this->id);
            $loc = new UserLocation($loc);
            $loc->location = $this->donor_location;
            $distance = $loc->radius ?: 50; // search in 50 km by default

            $sqlInner .= " INNER JOIN user_location
                            ON user_location.id = user.id ";
            $location_parts = UserLocation::getSQLFilterParts($loc, $distance, true, $loc->city, 'user.location');
            $values[":location_minLat"] = $location_parts['params'][':location_minLat'];
            $values[":location_minLon"] = $location_parts['params'][':location_minLon'];
            $values[":location_maxLat"] = $location_parts['params'][':location_maxLat'];
            $values[":location_maxLon"] = $location_parts['params'][':location_maxLon'];
            $values[":location_text"] = $location_parts['params'][':location_text'];
            $sqlFilter .= " AND ({$location_parts['firstcut_where']})" ;
        }

        $sqlFilter = ($this->forced) ? $sqlFilter : " AND (user_prefer.mailing = 0 OR user_prefer.`mailing` IS NULL) " . $sqlFilter;
        if($count) {
            $sql = "SELECT COUNT(DISTINCT(user.id))
                FROM user
                LEFT JOIN user_prefer
                ON user.id = user_prefer.user
                $sqlInner
                WHERE user.active = 1
                $sqlFilter";
            return (int) User::query($sql, $values)->fetchColumn();
        }

        $sql = "SELECT
                    user.id as user,
                    user.name as name,
                    user.email as email
                    $sqlFields
                FROM user
                LEFT JOIN user_prefer
                ON user_prefer.user = user.id
                $sqlInner
                WHERE user.active = 1
                $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC
                ";

        if ($limit) $sql .= "LIMIT $count, $limit ";

         if ($query = User::query($sql, $values)) {
            $receivers = $query->fetchAll(PDO::FETCH_CLASS, 'Goteo\Model\User');
        }

        return $receivers;
    }

    public function getPromotersSQL($lang = null, $prefix = '') {
        $values = array();
        $sqlInner  = '';
        $sqlFilter = '';
        $values[':prefix'] = $prefix;
        $sqlInner .= "INNER JOIN project
            ON project.owner = user.id
        ";

        if (isset($this->project_status) && $this->project_status > -1) {
            $sqlFilter .= "
                AND project.status = :project_status
                ";
            $values[':project_status'] = $this->project_status;
        } else if ($this->project_status == Project::STATUS_NEGOTIATION) {
            $sqlFilter .= "
                AND project.status = :project_status AND project.`id` NOT REGEXP '[0-9a-f]{32}'
            ";
            $values[':project_status'] = Project::STATUS_EDITING;
        }

        $this->projects = $this->getFilterProject($this->id);
        $this->calls = $this->getFilterCall($this->id);
        $this->channels = $this->getFilterNode($this->id);
        $this->matchers = $this->getFilterMatcher($this->id);

        if (!empty($this->projects)) {
            foreach(array_keys($this->projects) as $index => $id) {
                if ($index < 1) {
                    $sqlFilter .= " AND ( project.id =  :project_".$index;
                } else {
                    $sqlFilter .= " OR project.id = :project_".$index;
                }
                $values[':project_'.$index] = $id;
            }
            $sqlFilter .= ") ";
        }

        if (!empty($this->calls)) {
            $sqlInner .= "INNER JOIN call_project
                on call_project.project = project.id
            ";

            foreach(array_keys($this->calls) as $index => $id) {
                if ($index < 1) {
                    $sqlFilter .= " AND ( call_project.call =  :call_".$index;
                } else {
                    $sqlFilter .= " OR call_project.call = :call_".$index;
                }
                $values[':call_'.$index] = $id;
            }
            $sqlFilter .= ") ";
        }

        if (!empty($this->channels)) {
            $sqlInner .= "LEFT JOIN node_project
                on node_project.project_id = project.id
            ";

            foreach(array_keys($this->channels) as $index => $id) {
                $parts[] = ':nodes_' . $index;
                $values[':nodes_' . $index] = $id;
            }
            if($parts) $sqlFilter .= " AND ( node_project.node_id IN (" . implode(',', $parts) . ") OR  project.node IN (" . implode(',', $parts) . ") ) ";
        }

        if (!empty($this->matchers)) {
            if (!empty($this->matchers) && !empty($sqlInner)) {
                $sqlInner .= "INNER JOIN matcher_project
                    on matcher_project.project_id = project.id
                ";

                foreach(array_keys($this->matchers) as $index => $id) {
                    if ($index < 1) {
                        $sqlFilter .= " AND ( matcher_project.matcher_id =  :matchers_".$index;
                    } else {
                        $sqlFilter .= " OR matcher_project.matcher_id = :matchers_".$index;
                    }
                    $values[':matchers_'.$index] = $id;
                }
                $sqlFilter .= " ) ";
            }
        }

        if (isset($this->startdate)) {
            $sqlFilter .= " AND project.created BETWEEN :startdate";
            $values['startdate'] = $this->startdate;

            if(isset($this->enddate)) {
                $sqlFilter .= " AND :enddate";
                $values['enddate'] = $this->enddate;
            } else {
                $sqlFilter .= " AND curdate()";
            }
        } else if (isset($this->enddate)) {
            $sqlFilter .= " AND project.created < :enddate";
            $values['enddate'] = $this->enddate;
        }

        if (isset($lang)) {
            $parts = [];
            $sqlFilter .= " AND user.lang ";
            foreach($lang as $key => $value) {
                $parts[] = ':lang' . $key;
                $values[':lang' . $key] = $value;
            }
            if($parts) $sqlFilter .= " IN (" . implode(',', $parts) . ")";
        }

        if ($this->filter_location) {
            $loc = FilterLocation::get($this->id);
            $loc = new UserLocation($loc);
            $loc->location = $this->donor_location;
            $distance = $loc->radius ?: 50; // search in 50 km by default

            $sqlInner .= " INNER JOIN user_location
                            ON user_location.id = user.id ";
            $location_parts = UserLocation::getSQLFilterParts($loc, $distance, true, $loc->city, 'user.location');
            $values[":location_minLat"] = $location_parts['params'][':location_minLat'];
            $values[":location_minLon"] = $location_parts['params'][':location_minLon'];
            $values[":location_maxLat"] = $location_parts['params'][':location_maxLat'];
            $values[":location_maxLon"] = $location_parts['params'][':location_maxLon'];
            $values[":location_text"] = $location_parts['params'][':location_text'];
            $sqlFilter .= " AND ({$location_parts['firstcut_where']})" ;
        }

        $sqlFilter = ($this->forced) ? $sqlFilter : " AND (user_prefer.mailing = 0 OR user_prefer.`mailing` IS NULL) " . $sqlFilter;
        $sql = "SELECT
                    :prefix,
                    user.id as user,
                    user.name as name,
                    user.email as email
                FROM user
                LEFT JOIN user_prefer
                ON user_prefer.user = user.id
                $sqlInner
                WHERE user.active = 1
                $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC
                ";

        return [$sql,$values];
    }

    public function getMatchers($offset = 0, $limit = 0, $count = false, $lang = null) {

        $receivers = array();
        $values = array();
        $sqlInner  = '';
        $sqlFilter = '';
        $sqlInner .= "INNER JOIN matcher
            ON matcher.owner = user.id
            ";
        $this->matchers = $this->getFilterMatcher($this->id);

        if (!empty($this->matchers)) {
            $sqlFilter .= " AND
                matcher.id IN (:matchers)
                ";
            $values[':matchers'] = implode(',', array_keys($this->matchers));
        }

        if (isset($lang)) {
            $parts = [];
            $sqlFilter .= " AND user.lang ";
            foreach($lang as $key => $value) {
                $parts[] = ':lang' . $key;
                $values[':lang' . $key] = $value;
            }
            if($parts) $sqlFilter .= " IN (" . implode(',', $parts) . ")";
        }

        $sqlFilter = ($this->forced) ? $sqlFilter : " AND (user_prefer.mailing = 0 OR user_prefer.`mailing` IS NULL) " . $sqlFilter;
        if($count) {
            $sql = "SELECT COUNT(DISTINCT(user.id))
                FROM user
                LEFT JOIN user_prefer
                ON user.id = user_prefer.user
                $sqlInner
                WHERE user.active = 1
                $sqlFilter";
            return (int) User::query($sql, $values)->fetchColumn();
        }

        $sql = "SELECT
                    user.id as user,
                    user.name as name,
                    user.email as email
                FROM user
                LEFT JOIN user_prefer
                ON user_prefer.user = user.id
                $sqlInner
                WHERE user.active = 1
                $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC
                ";

        if ($limit) $sql .= "LIMIT $count, $limit ";

        if ($query = User::query($sql, $values)) {
            $receivers = $query->fetchAll(PDO::FETCH_CLASS, 'Goteo\Model\User');
        }

        return $receivers;
    }

    public function getMatchersSQL($lang = null, $prefix = '') {

        $values = array();
        $sqlInner  = '';
        $sqlFilter = '';
        $values[':prefix'] = $prefix;
        $sqlInner .= "INNER JOIN matcher
            ON matcher.owner = user.id
            ";
        $this->matchers = $this->getFilterMatcher($this->id);

        if (!empty($this->matchers)) {
            $sqlFilter .= " AND
                matcher.id IN (:matchers)
                ";
            $values[':matchers'] = implode(',', array_keys($this->matchers));
        }

        if (isset($lang)) {
            $parts = [];
            $sqlFilter .= " AND user.lang ";
            foreach($lang as $key => $value) {
                $parts[] = ':lang' . $key;
                $values[':lang' . $key] = $value;
            }
            if($parts) $sqlFilter .= " IN (" . implode(',', $parts) . ")";
        }

        $sqlFilter = ($this->forced) ? $sqlFilter : " AND (user_prefer.mailing = 0 OR user_prefer.`mailing` IS NULL) " . $sqlFilter;
        $sql = "SELECT
                    :prefix,
                    user.id as user,
                    user.name as name,
                    user.email as email
                FROM user
                LEFT JOIN user_prefer
                ON user_prefer.user = user.id
                $sqlInner
                WHERE user.active = 1
                $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC
                ";

        return [$sql,$values];
    }

    public function getTesters($offset = 0, $limit = 0, $count = false, $lang = null) {

        $receivers = array();
        $values = array();
        $sqlFields  = '';
        $sqlInner  = '';
        $sqlFilter = '';
        $sqlInner .= "INNER JOIN user_interest
            on user_interest.user = user.id";
        $sqlFilter .= " AND user_interest.interest = 15";

        if (isset($lang)) {
            $parts = [];
            $sqlFilter .= " AND user.lang ";
            foreach($lang as $key => $value) {
                $parts[] = ':lang' . $key;
                $values[':lang' . $key] = $value;
            }
            if($parts) $sqlFilter .= " IN (" . implode(',', $parts) . ")";
        }

        if($count) {
            $sql = "SELECT COUNT(DISTINCT(user.id)) FROM user $sqlInner WHERE user.active = 1 $sqlFilter";
            return (int) User::query($sql, $values)->fetchColumn();
        }

        $sql = "SELECT
                    user.id as user,
                    user.name as name,
                    user.email as email
                    $sqlFields
                FROM user
                $sqlInner
                WHERE user.active = 1
                $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC
                ";

        if ($limit) $sql .= "LIMIT $offset, $limit ";

        if ($query = User::query($sql, $values)) {
            $receivers = $query->fetchAll(PDO::FETCH_CLASS, 'Goteo\Model\User');
        }

        return $receivers;
    }

    public function getTestersSQL($lang = null, $prefix = '') {

        $values = array();
        $sqlFields  = '';
        $sqlInner  = '';
        $sqlFilter = '';
        $values[':prefix'] = $prefix;
        $sqlInner .= "INNER JOIN user_interest
            on user_interest.user = user.id";
        $sqlFilter .= " AND user_interest.interest = 15";

        if (isset($lang)) {
            $parts = [];
            $sqlFilter .= " AND user.lang ";
            foreach($lang as $key => $value) {
                $parts[] = ':lang' . $key;
                $values[':lang' . $key] = $value;
            }
            if($parts) $sqlFilter .= " IN (" . implode(',', $parts) . ")";
        }

        $sql = "SELECT
                    :prefix,
                    user.id as user,
                    user.name as name,
                    user.email as email
                    $sqlFields
                FROM user
                $sqlInner
                WHERE user.active = 1
                $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC
                ";

        return [$sql, $values];
    }

    public function getFiltered($offset = 0, $limit = 0, $count = false, $lang = null)
    {
        if ($this->role == $this::USER) {
            $result = $this->getUsers($offset, $limit, $count, $lang);
        } else if ($this->role == $this::DONOR) {
            $result = $this->getDonors($offset, $limit, $count, $lang);
        } else if ($this->role == $this::NODONOR) {
            $result = $this->getNoDonors($offset, $limit, $count, $lang);
        } else if ($this->role == $this::PROMOTER) {
            $result = $this->getPromoters($offset, $limit, $count, $lang);
        } else if ($this->role == $this::MATCHER) {
            $result = $this->getMatchers($offset, $limit, $count, $lang);
        } else if ($this->role == $this::TEST) {
            $result = $this->getTesters($offset, $limit, $count, $lang);
        }

        return $result;
    }

    public function getFilteredSQL($lang = null, $prefix = '')
    {
        if ($this->role == $this::USER) {
            list($sqlFilter, $values) = $this->getUsersSQL($lang, $prefix);
        } else if ($this->role == $this::DONOR) {
            list($sqlFilter, $values) = $this->getDonorsSQL($lang, $prefix);
        } else if ($this->role == $this::NODONOR) {
            list($sqlFilter, $values) = $this->getNoDonorsSQL($lang, $prefix);
        } else if ($this->role == $this::PROMOTER) {
            list($sqlFilter, $values) = $this->getPromotersSQL($lang, $prefix);
        } else if ($this->role == $this::MATCHER) {
            list($sqlFilter, $values) = $this->getMatchersSQL($lang, $prefix);
        } else if ($this->role == $this::TEST) {
            list($sqlFilter, $values) = $this->getTestersSQL($lang, $prefix);
        }

        return  [$sqlFilter, $values];
    }

}
