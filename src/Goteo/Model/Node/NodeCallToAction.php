<?php 

/**
 * Model for Node Call To Action
 */

 namespace Goteo\Model\Node;

 use Goteo\Model\Image;
 use Goteo\Application\Lang;
 use Goteo\Application\Config;
 use Goteo\Model\CallToAction;
 
 class NodeCallToAction extends \Goteo\Core\Model {

  protected $Table = 'node_call_to_action';
  protected static $Table_static = 'node_call_to_action';
  
  public
      $node_id,
      $call_to_action_id,
      $header,
      $title,
      $description,
      $icon,
      $action,
      $action_url,
      $style,
      $lang,
      $order,
      $active;

  /**
   * Get data about node faq
   *
   * @param   int    $id         check id.
   * @return  NodeCallToAction object
   */
  static public function get($node_id) {

    $lang = Lang::current();
    list($fields, $joins) = CallToAction::getLangsSQLJoins($lang);

    $sql="SELECT
                node_call_to_action.node_id,
                node_call_to_action.call_to_action_id,
                $fields,
                call_to_action.header,
                call_to_action.icon,
                call_to_action.icon_2,
                call_to_action.lang,
                node_call_to_action.style,
                node_call_to_action.order,
                node_call_to_action.active
          FROM node_call_to_action
          INNER JOIN call_to_action
            ON call_to_action.id = node_call_to_action.call_to_action_id
          $joins
          WHERE node_call_to_action.node_id = ?";
      if($query = self::query($sql, array($node_id))) {
        $list = [];
        foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $cta) {
          $cta->header = Image::get($cta->header);
          $list[] = $cta;
        }
        return $list;
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
      list($fields, $joins) = CallToAction::getLangsSQLJoins($lang, Config::get('sql_lang'));

      $filter = [];
      $values = [];

      if ($filters['node']) {
          $filter[] = "node_call_to_action.node_id = :node";
          $values[':node'] = $filters['node'];
      }

      if ($filters['active']) {
        $filter[] = "node_call_to_action.active";
      }

      if($filter) {
          $sql = " WHERE " . implode(' AND ', $filter);
      }

      $sql="SELECT
                  node_call_to_action.node_id,
                  node_call_to_action.call_to_action_id,
                  $fields,
                  call_to_action.header,
                  call_to_action.icon,
                  call_to_action.icon_2,
                  call_to_action.lang,
                  node_call_to_action.style,
                  node_call_to_action.order,
                  node_call_to_action.active
            FROM node_call_to_action
            INNER JOIN call_to_action
              ON call_to_action.id = node_call_to_action.call_to_action_id
            $joins
            $sql
            ORDER BY node_call_to_action.order
            LIMIT $offset, $limit";
      // die(\sqldbg($sql, $values));

      $list = [];
      if($query = self::query($sql, $values)) {
        foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $cta) {
          $cta->header = Image::get($cta->header);
          $list[] = $cta;
        }
      }
      return $list;

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
          $sql = "REPLACE INTO node_call_to_action (`node_id`, `call_to_action_id`, `order`, `active`, `style`) VALUES(:node_id, :call_to_action_id, :order, :active, :style)";
          $values = array(':node_id'=>$this->node_id, ':call_to_action_id'=>$this->call_to_action_id, ':order' => $this->order, ':active' => $this->active, ':style' => $this->style);
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


    /**
     * Validate.
     *
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
    public function validate(&$errors = array()) {
      if (empty($this->name)) 
        $errors[] = "The faq has no name";

      return empty($errors);
    }


 }