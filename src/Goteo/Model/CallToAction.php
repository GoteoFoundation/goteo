<?php 

/**
 * Model for Call To Action
 */

 namespace Goteo\Model;

 use Goteo\Model\Image;
 use Goteo\Application\Lang;
 use Goteo\Application\Config;
 
 class CallToAction extends \Goteo\Core\Model {

  protected $Table = 'call_to_action';
  protected static $Table_static = 'call_to_action';
  
  public
      $id,
      $header,
      $title,
      $description,
      $icon,
      $action,
      $action_url,
      $lang,
      $icon_2,
      $action_2,
      $action_url_2;

  public static function getLangFields() {
      return ['title', 'description', 'action', 'action_url', 'action_2', 'action_url_2'];
  }


  /**
   * Get data about node faq
   *
   * @param   int    $id         check id.
   * @return  CallToAction object
   */
  static public function get($id) {

    $lang = Lang::current();
    list($fields, $joins) = self::getLangsSQLJoins($lang);

    $sql="SELECT
                call_to_action.id,
                $fields,
                call_to_action.header,
                call_to_action.icon,
                call_to_action.icon_2,
                call_to_action.lang
          FROM call_to_action
          $joins
          WHERE call_to_action.id = ?";
    $query = static::query($sql, array($id));
    $item = $query->fetchObject(__CLASS__);

    if($item) {
      return $item;
    }

    throw new ModelNotFoundException("Call To Action not found for ID [$id]");
  }

    /**
   * Get data about node faq
   *
    * @param array filters
    * @param string node id
    * @param int limit items per page or 0 for unlimited
    * @param int page
    * @param int pages
    * @return array of CallToAction instances
    */
  static public function getList($filters = [], $offset = 0, $limit = 10, $count = false, $lang = null) { 
  
    $values = [];
    $sqlFilters = [];
    $where = '';

    if(!$lang) $lang = Lang::current();
    list($fields, $joins) = self::getLangsSQLJoins($lang);


    if ($count) {
      $sql = "SELECT COUNT(id) FROM call_to_action$sql";
      return (int) self::query($sql, $values)->fetchColumn();
    }

    if($filter) {
      $where = " WHERE " . implode(' AND ', $filter);
    }

    $sql="SELECT
        call_to_action.id,
        $fields,
        call_to_action.header,
        call_to_action.icon,
        call_to_action.icon_2,
        call_to_action.lang
    FROM call_to_action
    $joins
    $where
    LIMIT $offset,$limit";

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
      'header',
      'title',
      'description',
      'icon',
      'action',
      'action_url',
      'lang',
      'icon_2',
      'action_2',
      'action_url_2'
  );

    try {
        //automatic $this->id assignation
        $this->dbInsertUpdate($fields);

        return true;
    } catch(\PDOException $e) {
        $errors[] = "Call To Action save error: " . $e->getMessage();
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
        $errors[] = "The Call To Action has no name";

      return empty($errors);
    }


 }