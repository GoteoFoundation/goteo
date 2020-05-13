<?php 

/**
 * Model for Node Team
 */

 namespace Goteo\Model\Node;

 use Goteo\Model\Image;
 use Goteo\Application\Lang;
 use Goteo\Application\Config;
 
 class NodeFaqDownload extends \Goteo\Core\Model {

  protected $Table = 'node_faq_download';
  protected static $Table_static = 'node_faq_download';
  
  public
      $id,
      $type,
      $title,
      $icon,
      $description,
      $url,
      $order,
      $node_id;

    public static function getLangFields() {
        return ['title', 'description', 'url'];
    }


  /**
   * Get data about node faq download
   *
   * @param   int    $id         check id.
   * @return  NodeFaq object
   */
  public static function get($id, $type) {

    if(!$lang) $lang = Lang::current();
    list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

    $sql = "SELECT 
                  node_faq_download.id as id,
                  $fields,
                  node_faq_download.icon as icon,
                  node_faq_download.lang as lang,
                  node_faq_download.order as order,
                  node_faq_download.node_id as node_id
            FROM node_faq_download
            $joins
            WHERE node_faq_download.node_id = ?";

    $query = static::query($sql, array($id));
    $team = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);

    return $team;
  }

   /**
     * Node Faq Download listing
     *
     * @param array filters
     * @param string node id
     * @param int limit items per page or 0 for unlimited
     * @param int page
     * @param int pages
     * @return array of programs instances
     */
    static public function getList($filters = [], $offset = 0, $limit = 10, $count = false, $lang = null) {

        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $filter = [];
        $values = [];

        if ($filters['node']) {
            $filter[] = "node_faq_download.node_id = :node";
            $values[':node'] = $filters['node'];
        }

         if ($filters['type']) {
            $filter[] = "node_faq_download.type = :type";
            $values[':type'] = $filters['type'];
        }

        if($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        $sql="SELECT
                  node_faq_download.id as id,
                  $fields,
                  node_faq_download.icon as icon,
                  node_faq_download.order,
                  node_faq_download.node_id as node_id
              FROM node_faq_download
              $joins
              $sql
              ORDER BY node_faq_download.order ASC
              LIMIT $offset, $limit";
         //die(\sqldbg($sql, $values));
        $query = static::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
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
        'type',
        'title',
        'icon',
        'description',
        'url',
        'lang',
        'order',
        'node_id'
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
      if (empty($this->title)) 
        $errors[] = "The faq type has no title";

      return empty($errors);
    }


 }