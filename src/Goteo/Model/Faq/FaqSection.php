<?php 

/**
 * Model for Node Faq
 */

 namespace Goteo\Model\Faq;

 use Goteo\Model\Image;
 use Goteo\Model\Faq;
 use Goteo\Application\Lang;
 use Goteo\Application\Config;
 
 class FaqSection extends \Goteo\Core\Model {

  protected $Table = 'faq_section';
  protected static $Table_static = 'faq_section';
  
  public
      $id,
      $name,
      $slug,
      $icon,
      $banner_header,
      $button_action,
      $button_url,
      $lang,
      $order;


    public static function getLangFields() {
        return ['name', 'button_action', 'button_url'];
    }

    // fallbacks to getbyid
    public static function getBySlug($slug, $lang = null) {
        $post = self::get((string)$slug, $lang);
        if(!$post) {
            $post = self::get((int)$slug, $lang);
        }
        return $post;
    }

    public static function getById($id, $lang = null) {
        return self::get((int)$id, $lang);
    }

     /**
     * Get data about faq section
     *
     * @param   int    $id         check id.
     * @return  Workshop faq object
     */
    static public function get($id) {
        $sql="SELECT
                    faq_section.*
              FROM faq_section
              ";

        if(is_string($id)) {
            $sql .= "WHERE faq_section.slug = :slug";
            $values = [':slug' => $id];
        } else {
            $sql .= "WHERE faq_section.id = :id";
            $values = [':id' => $id];
        }

        //die(\sqldbg($sql, $values));
        $query = static::query($sql, $values);

        $item = $query->fetchObject(__CLASS__);

        if($item) {
          return $item;
        }

        throw new ModelNotFoundException("Faq section not found for ID [$id]");
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

        if($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        $sql="SELECT
                  faq_section.id as id,
                  $fields,
                  faq_section.slug as slug,
                  faq_section.icon as icon,
                  faq_section.banner_header as banner_header,
                  faq_section.order
              FROM faq_section
              $joins
              $sql
              ORDER BY faq_section.order ASC
              LIMIT $offset, $limit";
         //die(\sqldbg($sql, $values));
        $query = static::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }


  public function getFaqbySection($limit=0){

    return Faq::getList(['section'=>$this->id], 0, $limit);

  }


 public function getBannerHeaderImage() {
      if(!$this->bannerHeaderImageInstance instanceOf Image) {
          $this->bannerHeaderImageInstance = new Image($this->banner_header_image);
      }
      return $this->bannerHeaderImageInstance;
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
        'name',
        'slug',
        'icon',
        'banner_header',
        'button_action',
        'button_url',
        'lang',
        'order'
        ];

    try {
        //automatic $this->id assignation
        $this->dbInsertUpdate($fields);

        return true;
    } catch(\PDOException $e) {
        $errors[] = "Faq section save error: " . $e->getMessage();
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
        $errors[] = "The node faq has no name";

      return empty($errors);
    }


 }