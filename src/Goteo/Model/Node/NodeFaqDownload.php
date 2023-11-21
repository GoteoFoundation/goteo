<?php

/**
 * Model for Node Team
 */

 namespace Goteo\Model\Node;

 use Goteo\Core\Model;
 use Goteo\Application\Lang;
 use Goteo\Application\Config;

 class NodeFaqDownload extends Model {

  protected $Table = 'node_faq_download';
  protected static $Table_static = 'node_faq_download';

  public
      $id,
      $node_faq,
      $title,
      $icon,
      $description,
      $url,
      $order;

    public static function getLangFields(): array
    {
        return ['title', 'description', 'url'];
    }


  /**
   * Get data about node faq download
   *
   * @param   int    $id         check id.
   * @return  NodeFaq object
   */
  public static function get($id, $type) {

    $lang = Lang::current();
    list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

    $sql = "SELECT
                  node_faq_download.id as id,
                  $fields,
                  node_faq_download.icon as icon,
                  node_faq_download.lang as lang,
                  node_faq_download.node_faq as node_faq,
                  node_faq_download.order as order
            FROM node_faq_download
            $joins
            WHERE node_faq_download.node_id = ?";

    $query = static::query($sql, array($id));
    return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
  }

   /**
     * @return NodeFaqDownload[] | int
     */
    static public function getList(array $filters = [], int $offset = 0, int $limit = 10, bool $count = false, string $lang = null)
    {
        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $filter = [];
        $values = [];

        if ($filters['node']) {
            $filter[] = "node_faq_download.node_id = :node";
            $values[':node'] = $filters['node'];
        }

         if ($filters['node_faq']) {
            $filter[] = "node_faq_download.node_faq = :node_faq";
            $values[':node_faq'] = $filters['node_faq'];
        }

        if($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        if ($count) {
            $sql = "SELECT COUNT(*) FROM node_faq_download $joins $sql";
            return static::query($sql, $values)->fetchColumn();
        }

        $sql="SELECT
                  node_faq_download.id as id,
                  $fields,
                  node_faq_download.icon as icon,
                  node_faq_download.node_faq as node_faq,
                  node_faq_download.order
              FROM node_faq_download
              $joins
              $sql
              ORDER BY node_faq_download.order ASC
              LIMIT $offset, $limit";

        $query = static::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }


    /**
    * Save.
    *
    * @param   type array  $errors
    * @return  type bool   true|false
    */
    public function save(&$errors = array())
    {
        if (!$this->validate($errors))
            return false;

        $fields = [
            'id',
            'type',
            'title',
            'icon',
            'description',
            'url',
            'lang',
            'order',
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
      if (empty($this->title))
        $errors[] = "The faq type has no title";

      return empty($errors);
    }
 }
