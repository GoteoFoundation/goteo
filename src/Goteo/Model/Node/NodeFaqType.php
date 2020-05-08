<?php 

/**
 * Model for Node Team
 */

 namespace Goteo\Model\Node;

 use Goteo\Model\Image;
 use Goteo\Application\Lang;
 use Goteo\Application\Config;
 
 class NodeFaqType extends \Goteo\Core\Model {

  protected $Table = 'node_faq_type';
  protected static $Table_static = 'node_faq_type';
  
  public
      $id,
      $name,
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
   * Get data about node faq type
   *
   * @param   int    $id         check id.
   * @return  NodeFaq object
   */
  public static function get($id, $type) {

    if(!$lang) $lang = Lang::current();
    list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

    $sql = "SELECT 
                  node_faq_type.id as id,
                  $fields,
                  node_faq_type.banner_header_image as banner_header_image,
                  node_faq_type.banner_header_image_md as banner_header_image_md,        
                  node_faq_type.banner_header_image_sm as banner_header_image_sm,
                  node_faq_type.banner_header_image_xs as banner_header_image_xs,
                  node_faq_type.node_id as node_id
            FROM node_faq_type
            $joins
            WHERE node_faq_type.node_id = ?";

    $query = static::query($sql, array($id));
    $team = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);

    return $team;
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
        $errors[] = "The faq type has no name";

      return empty($errors);
    }


 }