<?php 

/**
 * Model for Node Faq
 */

 namespace Goteo\Model\Faq;

 use Goteo\Model\Image;
 use Goteo\Model\Faq;
 use Goteo\Application\Exception;
 use Goteo\Application\Lang;
 use Goteo\Application\Config;
 
 class FaqSubsection extends \Goteo\Core\Model {

  protected $Table = 'faq_subsection';
  protected static $Table_static = 'faq_subsection';
  
  public
      $id,
      $section_ide,
      $name,
      $lang,
      $order;


    public static function getLangFields() {
        return ['name'];
    }


     /**
     * Get data about faq subsection
     *
     * @param   int    $id         check id.
     * @return  Workshop faq object
     */
    static public function get($id) {
        $sql="SELECT
                    faq_subsection.*
              FROM faq_subsection
              WHERE faq_subsection.id = ?";
        $query = static::query($sql, array($id));
        $item = $query->fetchObject(__CLASS__);

        if($item) {
          return $item;
        }

        throw new Exception\ModelNotFoundException("Faq subsection not found for ID [$id]");
    }

    /**
     * Faq section listing
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

        if ($filters['section']) {
            $filter[] = "faq_subsection.section_id = :section_id";
            $values[':section_id'] = $filters['section'];
        }

        if($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        $sql="SELECT
                  faq_subsection.id as id,
                  faq_subsection.section_id as section_id,
                  $fields,
                  faq_subsection.order
              FROM faq_subsection
              $joins
              $sql
              ORDER BY faq_subsection.order ASC
              LIMIT $offset, $limit";
         //die(\sqldbg($sql, $values));
        $query = static::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

  public function getFaqbySubsection($limit=0){

    return Faq::getList(['subsection'=>$this->id], 0, $limit);

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

    $fields = [
        'id',
        'subsection_id',
        'name',
        'lang',
        'order'
        ];

    try {
        //automatic $this->id assignation
        $this->dbInsertUpdate($fields);

        return true;
    } catch(\PDOException $e) {
        $errors[] = "Faq subsection save error: " . $e->getMessage();
        return false;
    }
  }

    /**
     * Validate.
     *
     * @param   type array  $errors  
     * @return  type bool   true|false
     */
    public function validate(&$errors = array()) {
      if (empty($this->name)) 
        $errors[] = "The faq subsection has no name";

      return empty($errors);
    }


 }