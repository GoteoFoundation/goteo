<?php 

/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/


 namespace Goteo\Model\Image;

 use Goteo\Model\Image;
 use Goteo\Application\Lang;
 use Goteo\Application\Config;
 
 class Credits extends \Goteo\Core\Model {

  protected $Table = 'image_credits';
  protected static $Table_static = 'image_credits';
  
  public
      $id, // Image's name
      $credits,
      $lang;

    public static function getLangFields() {
        return ['credits'];
    }


  /**
   * Get data about image's credits
   *
   * @param   int    $id check name.
   * @return  Credits object
   */
  public static function get($id) {

    if(!$lang) $lang = Lang::current();
    list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

    $sql = "SELECT 
                  image_credits.id as id,
                  $fields,
                  image_credits.lang as lang
            FROM image_credits
            $joins
            WHERE image_credits.id = ?";

    $query = static::query($sql, array($id));
    // die(\sqldbg($sql, array($id)));
    $credits = $query->fetchObject(__CLASS__);

    return $credits;
  }

    /**
     * Credits listing
     *
     * @param array filters
     * @param string image names
     * @param int limit items per page or 0 for unlimited
     * @param int page
     * @param int pages
     * @return array of team member instances
     */
    static public function getList($filters = [], $offset = 0, $limit = 10, $count = false, $lang = null) {

      if(!$lang) $lang = Lang::current();
      list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

      $filter = [];
      $values = [];

      if ($filters['id']) {
          $filter[] = "image_credits.id = :id";
          $values[':id'] = $filters['id'];
      }

      if($filter) {
          $sql = " WHERE " . implode(' AND ', $filter);
      }

      $sql="SELECT
                  image_credits.id as id,
                  $fields,
                  image_credits.lang as lang
            FROM image_credits
            $joins
            $sql
            ORDER BY image_credits.id
            LIMIT $offset, $limit";
      // die(\sqldbg($sql, $values));
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
        'credits',
        'lang',
    );

    try {
        //automatic $this->id assignation
        $this->dbInsertUpdate($fields);

        return true;
    } catch(\PDOException $e) {
        $errors[] = "Image's credits save error: " . $e->getMessage();
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
      if (empty($this->id)) 
        $errors[] = "The image has no id";

      return empty($errors);
    }


 }