<?php
 namespace Goteo\Model\Node;

 use Goteo\Core\Model;
 use Goteo\Model\Image;
 use Goteo\Application\Lang;
 use Goteo\Application\Config;

 class NodeFaq extends Model
 {

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

     public static function getLangFields(): array
     {
         return ['name', 'banner_title', 'banner_description'];
     }


     /**
      * @return  NodeFaq
      */
     static public function get($id): NodeFaq
     {
         $sql = "SELECT
                    node_faq.*
              FROM node_faq
              WHERE node_faq.id = ?";
         $query = static::query($sql, array($id));
         $item = $query->fetchObject(__CLASS__);

         if ($item) {
             return $item;
         }

         throw new ModelNotFoundException("Node faq not found for ID [$id]");
     }

     /**
      * @return NodeFaq[]
      */
     static public function getList($filters = [], $offset = 0, $limit = 10, $count = false, $lang = null): array
     {
         if(!$lang) $lang = Lang::current();
         list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

         $filter = [];
         $values = [];

         if ($filters['node']) {
             $filter[] = "node_faq.node_id = :node";
             $values[':node'] = $filters['node'];
         }

         if($filter) {
             $sql = " WHERE " . implode(' AND ', $filter);
         }

         if ($count) {
             $sql = "SELECT COUNT(*) FROM node_faq $joins $sql";
             return (int)static::query($sql, $values)->fetchColumn();
         }

         $sql="SELECT
                  node_faq.id as id,
                  node_faq.slug as slug,
                  $fields,
                  node_faq.banner_header_image as banner_header_image,
                  node_faq.banner_header_image_md as banner_header_image_md,
                  node_faq.banner_header_image_sm as banner_header_image_sm,
                  node_faq.banner_header_image_xs as banner_header_image_xs,
                  node_faq.node_id as node_id
              FROM node_faq
              $joins
              $sql
              LIMIT $offset, $limit";

         $query = static::query($sql, $values);
         return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
     }

  public static function getBySlug(string $node_id, string $slug): NodeFaq
  {
    $lang = Lang::current();
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

    if (!$item instanceof NodeFaq)
        throw new ModelNotFoundException("Node faq not found for node [$node_id] and slug [$slug]");

    return $item;

  }

 public function getBannerHeaderImage(): Image
 {
      if(!$this->bannerHeaderImageInstance instanceOf Image) {
          $this->bannerHeaderImageInstance = new Image($this->banner_header_image);
      }
      return $this->bannerHeaderImageInstance;
  }

  public function getBannerHeaderImageMd(): Image
  {
      if(!$this->bannerHeaderImageInstanceMd instanceOf Image) {
          $this->bannerHeaderImageInstanceMd = new Image($this->banner_header_image_md);
      }
      return $this->bannerHeaderImageInstanceMd;
  }

  public function getBannerHeaderImageSm(): Image
  {
      if(!$this->bannerHeaderImageInstanceSm instanceOf Image) {
          $this->bannerHeaderImageInstanceSm = new Image($this->banner_header_image_sm);
      }
      return $this->bannerHeaderImageInstanceSm;
  }

  public function getBannerHeaderImageXs(): Image
  {
      if(!$this->bannerHeaderImageInstanceXs instanceOf Image) {
          $this->bannerHeaderImageInstanceXs = new Image($this->banner_header_image_xs);
      }
      return $this->bannerHeaderImageInstanceXs;
  }

  /**
   * @param   type array  $errors
   * @return  type bool
   */
  public function save(&$errors = array()) {

    if (!$this->validate($errors))
        return false;

    $fields = [
        'id',
        'name',
        'banner_title',
        'banner_description',
        'banner_header_image',
        'banner_header_image_ms',
        'banner_header_image_sm',
        'banner_header_image_xs',
        'node_id'
    ];

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

     /**
      * @return NodeFaqQuestion[]
      */
    public function getQuestions(): array
    {
        return NodeFaqQuestion::getList(['node_faq' => $this->id]);
    }
 }
