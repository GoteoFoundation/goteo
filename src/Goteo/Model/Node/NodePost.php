<?php

/**
 * Model for Node NodePost
 */

 namespace Goteo\Model\Node;

 use Goteo\Model\Image;
 use Goteo\Application\Lang;
 use Goteo\Application\Config;
 use Goteo\Model\Blog\Post as GeneralPost;

 class NodePost extends \Goteo\Core\Model {

  protected $Table = 'node_post';
  protected static $Table_static = 'node_post';

  public
      $node_id,
      $post_id,
      $order;


  /**
   * Get data about node faq
   *
   * @param   int    $id         check id.
   * @return  NodePosts object
   */
  static public function getNodePost($node_id, $post_id) {

    $lang = Lang::current();

    $sql="SELECT
                node_post.node_id node_id,
                node_post.post_id as post_id,
                node_post.order
          FROM node_post
          INNER JOIN post ON post.id = node_post.post_id
          $joins
          WHERE node_post.node_id = :node and node_post.post_id = :post
          ORDER BY node_post.order ASC";
          // die(\sqldbg($sql, [':node' => $node_id, ':post' => $post_id]));
      if($query = self::query($sql, [':node' => $node_id, ':post' => $post_id])) {
        return $query->fetchObject(__CLASS__);
      }

      return [];
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
      list($fields, $joins) = GeneralPost::getLangsSQLJoins($lang, Config::get('sql_lang'));

      $filter = [];
      $values = [];

      if ($filters['node']) {
          $filter[] = "node_post.node_id = :node";
          $values[':node'] = $filters['node'];
      }

      if ($filters['post']) {
          $filter[] = "node_post.post_id = :post";
          $values[':post'] = $filters['post'];
      }

      if($filter) {
          $sql = " WHERE " . implode(' AND ', $filter);
      }

      $sql="SELECT
                node_post.node_id as node_id,
                node_post.post_id as post_id,
                $fields,
                node_post.order
            FROM node_post
            INNER JOIN post ON post.id = node_post.post_id
            $joins
            $sql
            ORDER BY node_post.order ASC
            LIMIT $offset, $limit";
      // die(\sqldbg($sql, $values));

      if($query = self::query($sql, $values)) {
        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
      }
      return [];

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
          $sql = "REPLACE INTO node_post (`node_id`, `post_id`, `order`) VALUES(:node_id, :post_id, :order)";
          $values = array(':node_id'=>$this->node_id, ':post_id'=>$this->post_id, ':order' => $this->order);
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
    if(empty($this->node_id) || empty($this->post_id)) {
        throw new \Exception("Delete error: ID not defined!");
        return false;
    }
    try {
      $sql = 'DELETE FROM node_post WHERE node_id = :node and post_id = :post';
      $values = [':node' => $this->node_id, ':post' => $this->post_id];
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
      if (empty($this->post_id))
        $errors[] = "There is no post_id";

      return empty($errors);
    }
 }
