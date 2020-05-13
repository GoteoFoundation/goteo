<?php 

/**
 * Model for Node Faq
 */

 namespace Goteo\Model\Node;

 use Goteo\Model\Image;
 use Goteo\Application\Lang;
 use Goteo\Application\Config;
 
 class NodeFaq extends \Goteo\Core\Model {

  protected $Table = 'node_faq';
  protected static $Table_static = 'node_faq';
  
  public
      $id,
      $name,
      $question_color,
      $banner_title,
      $banner_description,
      $banner_header_image,
      $banner_header_image_md,
      $banner_header_image_sm,
      $banner_header_image_xs,
      $node_id;

    public static function getLangFields() {
        return ['name', 'banner_title', 'banner_description'];
    }


     /**
     * Get data about node faq
     *
     * @param   int    $id         check id.
     * @return  Workshop faq object
     */
    static public function get($id) {
        $sql="SELECT
                    node_faq.*
              FROM node_faq
              WHERE node_faq.node_id = ?";
        $query = static::query($sql, array($id));
        $item = $query->fetchObject(__CLASS__);

        if($item) {
          return $item;
        }

        throw new ModelNotFoundException("Node faq not found for ID [$id]");
    }

  /**
   * Get data about node faq by slug
   *
   * @param   int    $id         check id.
   * @return  NodeFaq object
   */
  public static function getBySlug($node_id, $slug) {

    if(!$lang) $lang = Lang::current();
    list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

    $sql = "SELECT 
                  node_faq.id as id,
                  node_faq.question_color as question_color,
                  $fields,
                  node_faq.banner_header_image as banner_header_image,
                  node_faq.banner_header_image_md as banner_header_image_md,        
                  node_faq.banner_header_image_sm as banner_header_image_sm,
                  node_faq.banner_header_image_xs as banner_header_image_xs,
                  node_faq.node_id as node_id
            FROM node_faq
            $joins
            WHERE node_faq.node_id = ? AND node_faq.slug = ?";

    $query = static::query($sql, [$node_id, $slug]);
    $item = $query->fetchObject(__CLASS__);

        if($item) {
          return $item;
        }

    return $item;
  }

 public function getBannerHeaderImage() {
      if(!$this->bannerHeaderImageInstance instanceOf Image) {
          $this->bannerHeaderImageInstance = new Image($this->banner_header_image);
      }
      return $this->bannerHeaderImageInstance;
  }

  public function getBannerHeaderImageMd() {
      if(!$this->bannerHeaderImageInstanceMd instanceOf Image) {
          $this->bannerHeaderImageInstanceMd = new Image($this->banner_header_image_md);
      }
      return $this->bannerHeaderImageInstanceMd;
  }

  public function getBannerHeaderImageSm() {
      if(!$this->bannerHeaderImageInstanceSm instanceOf Image) {
          $this->bannerHeaderImageInstanceSm = new Image($this->banner_header_image_sm);
      }
      return $this->bannerHeaderImageInstanceSm;
  }

  public function getBannerHeaderImageXs() {
      if(!$this->bannerHeaderImageInstanceXs instanceOf Image) {
          $this->bannerHeaderImageInstanceXs = new Image($this->banner_header_image_xs);
      }
      return $this->bannerHeaderImageInstanceXs;
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
        'banner_title',
        'banner_description',
        'banner_header_image',
        'banner_header_image_ms',
        'banner_header_image_sm',
        'banner_header_image_xs',
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
      if (empty($this->name)) 
        $errors[] = "The faq has no name";

      return empty($errors);
    }


 }