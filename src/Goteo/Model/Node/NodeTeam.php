<?php 

/**
 * Model for Node Team
 */

 namespace Goteo\Model\Node;

 use Goteo\Model\Image;
 use Goteo\Application\Lang;
 use Goteo\Application\Config;
 
 class NodeTeam extends \Goteo\Core\Model {

  protected $Table = 'node_team';
  protected static $Table_static = 'node_team';
  
  public
      $id,
      $name,
      $role,
      $image,
      $node_id,
      $order;

    public static function getLangFields() {
        return ['role'];
    }


  /**
   * Get data about node team
   *
   * @param   int    $id         check id.
   * @return  NodeTeam object
   */
  public static function get($id) {

    if(!$lang) $lang = Lang::current();
    list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

    $sql = "SELECT 
                  node_team.id as id,
                  node_team.name as name,
                  $fields,
                  node_team.image as image,
                  node_team.node_id as node_id
            FROM node_team
            $joins
            WHERE node_team.id = ?";

    $query = static::query($sql, array($id));
    $team = $query->fetchObject(__CLASS__);

    return $team;
  }

    /**
     * Node Team listing
     *
     * @param array filters
     * @param string node id
     * @param int limit items per page or 0 for unlimited
     * @param int page
     * @param int pages
     * @return array of team member instances
     */
    static public function getList($filters = [], $offset = 0, $limit = 10, $count = false, $lang = null) {

      if(!$lang) $lang = Lang::current();
      list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

      $filter = [];
      $values = [];

      if ($filters['node']) {
          $filter[] = "node_team.node_id = :node";
          $values[':node'] = $filters['node'];
      }

      if($filter) {
          $sql = " WHERE " . implode(' AND ', $filter);
      }

      $sql="SELECT
                  node_team.id as id,
                  node_team.name as name,
                  $fields,
                  node_team.image as image,
                  node_team.node_id as node_id
            FROM node_team
            $joins
            $sql
            ORDER BY node_team.order
            LIMIT $offset, $limit";
      // die(\sqldbg($sql, $values));
      $query = static::query($sql, $values);
      return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
  }

  public function getImage() {
    if (!$this->image instanceOf Image) {
      $this->image = new Image($this->image);
    }

    return $this->image;
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

    $fields = array(
        'id',
        'name',
        'role',
        'image',
        'node_id',
        'order'
    );

    try {
        //automatic $this->id assignation
        $this->dbInsertUpdate($fields);

        return true;
    } catch(\PDOException $e) {
        $errors[] = "Node team save error: " . $e->getMessage();
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
      if (empty($this->id)) 
        $errors[] = "The team member has no name";

      return empty($errors);
    }


 }