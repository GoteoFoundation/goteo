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
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ModelException;
use Goteo\Model\Project;
use Goteo\Model\Call;
use Goteo\Model\Invest;

/**
 * Origin Model
 */
class Origin extends \Goteo\Core\Model {
    public $id,
           $tag ,
           $category ,
           $type ,
           $project_id,
           $invest_id,
           $call_id,
           $counter = 0,
           $created_at,
           $modified_at;

    /**
     * Get instance of origin by id
     * @return [type] [description]
     */
    static public function get($id) {
        if ($query = static::query("SELECT * FROM `origin` WHERE `id` = ?", $id)) {
            if( $origin = $query->fetchObject(__CLASS__) )
                return $origin;
        }
        throw new ModelNotFoundException("Origin [$id] not found");
    }

    /**
     * Get instance of origin already in the table or creates a new one
     * if not exists. Search by any given array of key => values
     * @return [type] [description]
     */
    static public function getFromArray(array $array) {
        $values = [];
        $search = [];
        foreach($array as $key => $val) {
            $values[":$key"] = $val;
            $search[] = "`$key` = :$key";
        }
        if ($query = static::query("SELECT * FROM `origin` WHERE " . implode(' AND ', $search), $values)) {
            if( $origin = $query->fetchObject(__CLASS__) )
                return $origin;
        }
        return new self($array);
    }

    static public function getList($filter = [], $offset = 0, $limit = 10, $count = false) {
        $values = [];
        $where = [];
        $join = '';
        if(isset($filter['project'])) {
            $where[] = 'origin.project_id = :project';
            $values[':project'] = $filter['project'];
        }
        if(isset($filter['project_invests'])) {
            $where[] = 'invest.project = :project';
            $values[':project'] = $filter['project_invests'];
            $join = 'RIGHT JOIN invest ON invest.id = origin.invest_id';
        }
        if(isset($filter['invest'])) {
            $where[] = 'origin.invest_id = :invest';
            $values[':invest'] = $filter['invest'];
        }
        if(isset($filter['call'])) {
            $where[] = 'origin.call_id = :call';
            $values[':call'] = $filter['call'];
        }
        if(isset($filter['type']) && in_array($filter['type'], ['ua', 'referer'])) {
            $where[] = 'origin.type = :type';
            $values[':type'] = $filter['type'];
        }
        if(isset($filter['from'])) {
            $where[] = 'origin.created_at >= :from';
            $values[':from'] = $filter['from'];
        }
        if(isset($filter['to'])) {
            $where[] = 'origin.modified_at <= :to';
            $values[':to'] = $filter['to'];
        }
        if(isset($filter['category'])) {
            $where[] = 'origin.category = :category';
            $values[':category'] = $filter['category'];
        }
        if(isset($filter['tag'])) {
            $where[] = 'origin.tag = :tag';
            $values[':tag'] = $filter['tag'];
        }

        $sqlFilter = $where ? ' WHERE '. implode(' AND ',  $where) : '';
        if($count) {
            if($count === 'all') {
            }
            else {
                $what = 'SUM(origin.counter) AS total';
            }
            // Return count
            $sql = "SELECT DISTINCT $what
                FROM origin
                $join
                $sqlFilter";

                // echo sqldbg($sql, $values);

            // if($count === 'all') {
            //     $ob = self::query($sql, $values)->fetchObject();
            //     return ['amount' => (float) $ob->total_amount, 'invests' => (int) $ob->total_invests, 'users' => (int) $ob->total_users];
            // }
            $total = self::query($sql, $values)->fetchColumn();
            return (int) $total;
        }

        $offset = (int) $offset;
        $limit = (int) $limit;
        $sql = "SELECT * FROM origin
                $join
                $sqlFilter ORDER BY origin.counter DESC LIMIT $offset, $limit";
        // echo sqldbg($sql, $values);
        $query = self::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    static public function getInvestsStats(Model $model = null, $group = 'referer', $group_by = 'tag', $filters = []) {
        $values = [':type' => $group];
        $add_sql = '';
        if($model instanceOf Project) {
            if($model->id) {
                $values[':project'] = $model->id;
                $add_sql = 'AND invest.project = :project';
            } else {
                // Only projects
                $add_sql = 'AND !ISNULL(invest.project)';
            }
        } elseif(!is_null($model)) {
            throw new ModelException("[$model] should be an instance of Project or null"); // TODO: add call in the future
        }

        $group_by = ($group_by === 'category') ? 'category' : 'tag';

        if($filters['from']) {
            if(!\date_valid($filters['from'])) throw new ModelException("Invalid date 'from' [{$filters['from']}]");
            $add_sql .= ' AND invest.datetime >= :from';
            $values[':from'] = $filters['from'];
        }
        if($filters['to']) {
            if(!\date_valid($filters['to'])) throw new ModelException("Invalid date 'to' [{$filters['to']}]");
            $add_sql .= ' AND invest.datetime <= :to';
            $values[':to'] = $filters['to'];
        }
        // Project invests stats
        $sql = "SELECT
        origin.tag,
        origin.category,
        IF (MIN(origin.created_at), MIN(origin.created_at), MIN(invest.datetime)) AS created,
        IF (MAX(origin.modified_at), MAX(origin.modified_at), MAX(invest.datetime)) as updated,
        IF (SUM(origin.counter), SUM(origin.counter), COUNT(invest.id)) AS counter
        FROM invest
        LEFT JOIN origin ON origin.invest_id = invest.id AND origin.type = :type
        WHERE invest.status IN (". implode(',', Invest::$ACTIVE_STATUSES) . ")
        $add_sql
        GROUP BY origin.$group_by ORDER BY counter DESC";

        // echo sqldbg($sql, $values);
        $query = self::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_OBJ);
    }

    static public function getModelStats(Model $model = null, $group = 'referer', $group_by = 'tag', $filters = []) {
        $values = [':type' => $group];

        $add_sql = '';
        if($model instanceOf Project) {
            if($model->id) {
                $values[':project'] = $model->id;
                $add_sql = 'AND origin.project_id = :project';
            } else {
                // Only projects
                $add_sql = 'AND !ISNULL(origin.project_id)';
            }
        } elseif($model instanceOf Call) {
            if($model->id) {
                $values[':call'] = $model->id;
                $add_sql = 'AND origin.call_id = :call';
            } else {
                // Only calls
                $add_sql = 'AND !ISNULL(origin.call_id)';
            }
        } elseif(!is_null($model)) {
            throw new ModelException("[$model] should be an instance of Project, Call or null"); // TODO: add call in the future
        }

        $group_by = ($group_by === 'category') ? 'category' : 'tag';
        if($filters['from']) {
            if(!\date_valid($filters['from'])) throw new ModelException("Invalid date 'from' [{$filters['from']}]");
            $add_sql .= ' AND origin.created_at >= :from';
            $values[':from'] = $filters['from'];
        }
        if($filters['to']) {
            if(!\date_valid($filters['to'])) throw new ModelException("Invalid date 'to' [{$filters['to']}]");
            $add_sql .= ' AND origin.modified_at <= :to';
            $values[':to'] = $filters['to'];
        }
        if($filters['call']) {
            $add_sql .= ' AND origin.project_id IN (SELECT project FROM call_project a WHERE a.call=:call)';
            $values[':call'] = $filters['call'];
        }
        if($filters['matcher']) {
            $add_sql .= ' AND origin.project_id IN (SELECT project_id FROM matcher_project b WHERE b.matcher_id=:matcher)';
            $values[':matcher'] = $filters['matcher'];
        }
        if($filters['channel']) {
            $add_sql .= ' AND origin.project_id IN (SELECT id FROM project c WHERE c.node=:channel)';
            $values[':channel'] = $filters['channel'];
        }
        if($filters['user']) {
            $add_sql .= ' AND origin.project_id IN (SELECT id FROM project d WHERE d.owner=:user)';
            $values[':user'] = $filters['user'];
        }
        if($filters['consultant']) {
            $add_sql .= ' AND origin.project_id IN (SELECT project FROM user_project e WHERE e.user=:consultant)';
            $values[':consultant'] = $filters['consultant'];
        }

        // Project visit stats by default
        $sql = "SELECT
        origin.tag,
        origin.category,
        MIN(origin.created_at) AS created,
        MAX(origin.modified_at) AS updated,
        SUM(origin.counter) AS counter
        FROM origin
        WHERE origin.type = :type
        $add_sql
        GROUP by origin.$group_by ORDER BY counter DESC";

        // die(sqldbg($sql, $values));
        $query = self::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Guardar.
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
    public function save(&$errors = array()) {

        if(!$this->validate($errors)) return false;

        if(!$this->created_at) $this->created_at = date('Y-m-d H:i:s');
        // Always increment counter
        $this->counter++;

        try {
            $this->dbInsertUpdate(['tag', 'category', 'type', 'project_id', 'invest_id', 'call_id', 'counter', 'created_at']);
            return true;
        }
        catch(\PDOException $e) {
            $errors[] = 'Error saving origin: ' . $e->getMessage();
        }

        return false;
    }

    /**
     * Validar. check if origin is not-duplicated
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
    public function validate(&$errors = array()) {
        return empty($errors);
    }

}
