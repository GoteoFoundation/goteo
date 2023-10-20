<?php

/**
 * Model for Node Stories
 */

 namespace Goteo\Model\Node;

 use Goteo\Model\Image;
 use Goteo\Application\Lang;
 use Goteo\Application\Config;
 use Goteo\Model\Stories;

 class NodeStories extends \Goteo\Core\Model {

  protected $Table = 'node_stories';
  protected static $Table_static = 'node_stories';

  public
      $node_id,
      $stories_id,
      $order,
      $image,
      $pool_image,
      $background_image;

  /**
   * Get data about node faq
   *
   * @param   int    $id         check id.
   * @return  NodeStories object
   */
  static public function getNodeStory($node_id, $stories_id) {

    $lang = Lang::current();
    list($fields, $joins) = Stories::getLangsSQLJoins($lang);

    $sql="SELECT
                IFNULL(node_stories.node_id, stories.node) as node_id,
                node_stories.stories_id as stories_id,
                node_stories.order,
                $fields,
                stories.image,
                stories.pool_image,
                stories.background_image,
                stories.url
          FROM node_stories
          INNER JOIN stories ON stories.id = node_stories.stories_id
          $joins
          WHERE node_stories.node_id = :node and node_stories.stories_id = :stories
          ORDER BY node_stories.order ASC";

      if($query = self::query($sql, [':node' => $node_id, ':stories' => $stories_id])) {
        return $query->fetchObject(__CLASS__);
      }

      return [];
  }

    /**
     * Node Stories listing
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
      list($fields, $joins) = Stories::getLangsSQLJoins($lang, Config::get('sql_lang'));

      $filter = [];
      $values = [];

      if ($filters['node']) {
          $filter[] = "node_stories.node_id = :node";
          $values[':node'] = $filters['node'];
      }

      if($filter) {
          $sql = " WHERE " . implode(' AND ', $filter);
      }

      $sql="SELECT
                IFNULL(node_stories.node_id, stories.node) as node_id,
                node_stories.stories_id as stories_id,
                node_stories.order,
                $fields,
                stories.image,
                stories.pool_image,
                stories.background_image,
                stories.url
            FROM node_stories
            INNER JOIN stories ON stories.id = node_stories.stories_id
            $joins
            $sql
            ORDER BY node_stories.order ASC
            LIMIT $offset, $limit";
      // die(\sqldbg($sql, $values));

      if($query = self::query($sql, $values)) {
        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
      }
      return [];

  }

  public function getImage() {
    if(!$this->imageInstance instanceOf Image) {
        $this->imageInstance = new Image($this->image);
    }
    return $this->imageInstance;
  }


  public function getBackgroundImage() {
    if(!$this->BackgroundImageInstance instanceOf Image) {
        $this->BackgroundImageInstance = new Image($this->background_image);
    }
    return $this->BackgroundImageInstance;
  }

  /**
   * Save.
   *
   * @param   type array  $errors
   * @return  type bool   true|false
   */
  public function save (&$errors = array()) {
    if (!$this->validate($errors)) return false;

    try {
          $sql = "REPLACE INTO node_stories (`node_id`, `stories_id`, `order`) VALUES(:node_id, :stories_id, :order)";
          $values = array(':node_id'=>$this->node_id, ':stories_id'=>$this->stories_id, ':order' => $this->order);
    if (self::query($sql, $values)) {
        return true;
    } else {
        $errors[] = "$sql <pre>".print_r($values, true)."</pre>";
    }
    } catch(\PDOException $e) {
      $errors[] = $e->getMessage();
      return false;
    }

  }

	public function dbDelete(array $where = ['id']) {
    if(empty($this->node_id) || empty($this->stories_id)) {
        throw new \Exception("Delete error: ID not defined!");
        return false;
    }
    try {
      $sql = 'DELETE FROM node_stories WHERE node_id = :node and stories_id = :stories';
      $values = [':node' => $this->node_id, ':stories' => $this->stories_id];
        self::query($sql, $values);
    } catch (\PDOException $e) {
        throw new \Exception("Delete error in $sql");
        return false;
    }

    return true;
  }

    /**
     * Validate.
     *
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
    public function validate(&$errors = array()) {
      if (empty($this->node_id))
        $errors[] = "There is no node_id";
      if (empty($this->stories_id))
        $errors[] = "There is no stories_id";

      return empty($errors);
    }


 }
