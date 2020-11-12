<?php 

/**
 * Model for Node Sections
 */

 namespace Goteo\Model\Node;

 use Goteo\Model\Image;
 use Goteo\Application\Lang;
 use Goteo\Application\Config;
 
 class NodeSections extends \Goteo\Core\Model {

  protected $Table = 'node_sections';
  protected static $Table_static = 'node_sections';
  
  public
      $id,
      $node,
      $section,
      $main_title,
      $main_description,
      $main_image,
      $main_button,
      $order;

    public static function getLangFields() {
        return ['main_title', 'main_description', 'main_button'];
    }


  /**
   * Get data about Node Sections
   *
   * @param   int    $id         check id.
   * @return  NodeSections object
   */
  public static function get($id) {

    if(!$lang) $lang = Lang::current();
    list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

    $sql = "SELECT 
                  node_sections.id as id,
                  node_sections.node as node,
                  node_sections.section as section,
                  $fields,
                  node_sections.main_image as main_image,
                  node_sections.order
            FROM node_sections
            $joins
            WHERE node_sections.id = ?";

    $query = static::query($sql, array($node));
    $sections = $query->fetchObject(__CLASS__);

    return $sections;
  }

  public function getImage() {
    if (!$this->main_image instanceOf Image) {
      $this->main_image = new Image($this->main_image);
    }

    return $this->main_image;
  }

      /**
     * Node Sections listing
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
          $filter[] = "node_sections.node = :node";
          $values[':node'] = $filters['node'];
      }

      if ($filters['section']) {
        $filter[] = "node_sections.section = :section";
        $values[':section'] = $filters['section'];
    }

      if($filter) {
          $sql = " WHERE " . implode(' AND ', $filter);
      }

      $sql="SELECT
                node_sections.id as id,
                node_sections.node as node,
                node_sections.section as section,
                $fields,
                node_sections.main_image as main_image,
                node_sections.order
            FROM node_sections
            $joins
            $sql
            ORDER BY node_sections.order ASC
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
  public function save(&$errors = array()) {

    if (!$this->validate($errors))
        return false;

    $fields = array(
        'id',
        'node',
        'section',
        'maint_title',
        'main_description',
        'main_image',
        'order'
    );

    try {
        //automatic $this->id assignation
        $this->dbInsertUpdate($fields);

        return true;
    } catch(\PDOException $e) {
        $errors[] = "Node Sections save error: " . $e->getMessage();
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
      if (empty($this->node)) 
        $errors[] = "The node sections member has no node";

      if (empty($this->section)) 
        $errors[] = "The node sections member has no section";

      return empty($errors);
    }


 }