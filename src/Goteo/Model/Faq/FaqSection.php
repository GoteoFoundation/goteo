<?php

 namespace Goteo\Model\Faq;

 use Goteo\Application\Config;
 use Goteo\Application\Exception\ModelNotFoundException;
 use Goteo\Application\Lang;
 use Goteo\Core\Model;
 use Goteo\Model\Faq;
 use Goteo\Model\Image;

 class FaqSection extends Model {

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


    public static function getLangFields(): array
    {
        return ['name', 'button_action', 'button_url'];
    }

    public static function getBySlug(string $slug, $lang = null): FaqSection
    {
        return self::get($slug, $lang);
    }

    public static function getById(int $id): FaqSection
    {
        return self::get($id);
    }

     /**
      * @throws ModelNotFoundException
      */
    static public function get($id): FaqSection
    {
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

        $query = static::query($sql, $values);

        $item = $query->fetchObject(__CLASS__);

        if(!$item)
            throw new ModelNotFoundException("Faq section not found for ID [$id]");

        return $item;
    }


     /**
      * @return array [] FaqSection
      */
    static public function getList(array $filters = [], int $offset = 0, int $limit = 10, $lang = null)
    {

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
        $query = static::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

     static public function getListCount(array $filters = [], $lang = null): int
     {
         if(!$lang) $lang = Lang::current();
         list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

         $filter = [];
         $values = [];

         if($filter) {
             $sql = " WHERE " . implode(' AND ', $filter);
         }

         $sql = "SELECT
              count(faq_section.id)
          FROM faq_section
          $joins
          $sql";
         $query = static::query($sql, $values);
         return $query->fetchColumn();
    }

    /**
     * @return Faq[]
     */
    public function getFaqs(int $limit = 0): array
    {
        $count = Faq::getListCount(['section' => $this->id]);
        return Faq::getList(['section'=>$this->id], 0, $count);
    }


    public function getBannerHeaderImage(): Image {
      if(!$this->bannerHeaderImageInstance instanceOf Image) {
          $this->bannerHeaderImageInstance = new Image($this->banner_header_image);
      }

      return $this->bannerHeaderImageInstance;
    }

    public function save(&$errors = array()): bool
    {
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

    public function validate(&$errors = array()): bool
    {
      if (empty($this->name))
        $errors[] = "The faq section has no name";

      return empty($errors);
    }
 }
