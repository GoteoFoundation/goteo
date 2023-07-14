<?php

/**
 * Model for Node Faq
 */

 namespace Goteo\Model\Faq;

 use Goteo\Application\Exception\ModelNotFoundException;
 use Goteo\Core\Model;
 use Goteo\Model\Image;
 use Goteo\Model\Faq;
 use Goteo\Application\Exception;
 use Goteo\Application\Lang;
 use Goteo\Application\Config;

 class FaqSubsection extends Model {

    protected $Table = 'faq_subsection';
    protected static $Table_static = 'faq_subsection';

    public
      $id,
      $section_id,
      $name,
      $lang,
      $order;


    public static function getLangFields(): array
    {
        return ['name'];
    }

     /**
      * @throws ModelNotFoundException
      */
    static public function get($id): FaqSubsection {
        $sql="SELECT
                    faq_subsection.*
              FROM faq_subsection
              WHERE faq_subsection.id = ?";
        $query = static::query($sql, array($id));
        $item = $query->fetchObject(__CLASS__);

        if($item) {
          return $item;
        }

        throw new ModelNotFoundException("Faq subsection not found for ID [$id]");
    }

    /**
     * @return FaqSubsection [] | int
     */
    static public function getList(array $filters = [], int $offset = 0, int $limit = 10, bool $count = false, string $lang = null)
    {
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

        if ($count) {
            $sql = "SELECT
                          count(faq_subsection.id)
                      FROM faq_subsection
                      $joins
                      $sql";

            $query = static::query($sql, $values);
            return $query->fetchColumn();
        }

        $sql="SELECT
                  faq_subsection.id,
                  faq_subsection.section_id,
                  $fields,
                  faq_subsection.order
              FROM faq_subsection
              $joins
              $sql
              ORDER BY faq_subsection.order ASC
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

        if ($filters['section']) {
            $filter[] = "faq_subsection.section_id = :section_id";
            $values[':section_id'] = $filters['section'];
        }

        if($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        $sql = "SELECT
                      count(faq_subsection.id)
                  FROM faq_subsection
                  $joins
                  $sql";

        $query = static::query($sql, $values);
        return $query->fetchColumn();
    }

     /**
      * @return Faq []
      */
    public function getFaqs(): array
    {
        $total = Faq::getListCount(['subsection' => $this->id]);
        return Faq::getList(['subsection'=>$this->id], 0, $total);
    }

    public function save(&$errors = array()): bool
    {

        if (!$this->validate($errors))
            return false;

        $fields = [
            'id',
            'section_id',
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

    public function validate(&$errors = array()): bool
    {
      if (empty($this->name))
        $errors[] = "The faq subsection has no name";

      return empty($errors);
    }
 }
