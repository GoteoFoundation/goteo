<?php

/**
 * Model for Node Sections
 */

 namespace Goteo\Model\Node;

 use Goteo\Core\Model;
 use Goteo\Model\Image;
 use Goteo\Application\Lang;
 use Goteo\Application\Config;
 use Goteo\Library\Text;

 class NodeSections extends Model {

  protected $Table = 'node_sections';
  protected static $Table_static = 'node_sections';

  const SECTION_MAP             = 'map';
  const SECTION_RESOURCES       = 'resources';
  const SECTION_CALL_TO_ACTION  = 'call_to_action';
  const SECTION_PROJECTS        = 'projects';
  const SECTION_POSTS           = 'posts';
  const SECTION_PROGRAM         = 'program';
  const SECTION_STORIES         = 'stories';
  const SECTION_WORKSHOPS       = 'workshops';
  const SECTION_TEAM            = 'team';
  const SECTION_SPONSORS        = 'sponsors';
  const SECTION_DATA_SETS       = 'data_sets';
  const SECTION_FOOTER          = 'footer';



  static array $SECTIONS = [
      self::SECTION_MAP,
      self::SECTION_RESOURCES,
      self::SECTION_CALL_TO_ACTION,
      self::SECTION_PROJECTS,
      self::SECTION_POSTS,
      self::SECTION_PROGRAM,
      self::SECTION_STORIES,
      self::SECTION_WORKSHOPS,
      self::SECTION_TEAM,
      self::SECTION_SPONSORS,
      self::SECTION_DATA_SETS,
      self::SECTION_FOOTER
  ];

  public
      $id,
      $node,
      $section,
      $main_title,
      $main_description,
      $main_image,
      $main_button,
      $order = 1;

    public static function getLangFields(): array
    {
        return ['main_title', 'main_description', 'main_button'];
    }

    public static function get($id): NodeSections
    {
      $lang = Lang::current();
      list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));
      $sql = "
          SELECT
              node_sections.id as id,
              node_sections.node as node,
              node_sections.section as section,
              $fields,
              node_sections.main_image as main_image,
              node_sections.order
          FROM node_sections
          $joins
          WHERE node_sections.id = ?";

      $query = static::query($sql, $id);
      return $query->fetchObject(__CLASS__);
    }

    public function getImage() {
        if (!$this->main_image instanceOf Image) {
          $this->main_image = new Image($this->main_image);
        }

        return $this->main_image;
    }

      /**
     * Node Sections listing
     *
     * @param array filters
     * @param string node id
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

      if ($filters['node']) {
          $filter[] = "node_sections.node = :node";
          $values[':node'] = $filters['node'];
      }

      if ($filters['section']) {
        $filter[] = "node_sections.section = :section";
        $values[':section'] = $filters['section'];
    }

      if($filter) {
          $sql = " WHERE " . implode(' AND ', $filter);
      }

      if ($count) {
        $sql = "SELECT count(node_sections.id)
                FROM node_sections
                $sql";
        return (int) self::query($sql, $values)->fetchColumn(0);
      }

      $sql="SELECT
                node_sections.id as id,
                node_sections.node as node,
                node_sections.section as section,
                $fields,
                node_sections.main_image as main_image,
                node_sections.order
            FROM node_sections
            $joins
            $sql
            ORDER BY node_sections.order ASC
            LIMIT $offset, $limit";
      // die(\sqldbg($sql, $values));

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

    try {
        //automatic $this->id assignation
        // $this->dbInsertUpdate($fields);
        $sql = "REPLACE INTO node_sections (`id`,`node`,`section`,`main_title`,`main_description`,`main_button`,`main_image`,`order`)
                        VALUES (:id, :node, :section, :main_title, :main_description, :main_button, :main_image, :order)";

        $values = [
          'id' => $this->id,
          'node' => $this->node,
          'section' => $this->section,
          'main_title' => $this->main_title,
          'main_description' => $this->main_description,
          'main_button' => $this->main_button,
          'main_image' => $this->main_image,
          'order' => $this->order
        ];
        self::query($sql, $values);

        return true;
    } catch(\PDOException $e) {
        $errors[] = "Node Sections save error: " . $e->getMessage();
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
      if (empty($this->node))
        $errors[] = "The node sections member has no node";

      if (empty($this->section))
        $errors[] = "The node sections member has no section";

      return empty($errors);
    }

    /*
    * Order to set it at the end
    */
    public static function next ($node) {
      $query = self::query('SELECT MAX(`order`) FROM node_sections WHERE node = :node'
          , array(':node'=>$node));
      $order = $query->fetchColumn(0);
      return $order++;
    }


    public static function getSectionNames(): array
    {
      return [
        self::SECTION_MAP => Text::get('admin-channelsection-map'),
        self::SECTION_RESOURCES => Text::get('admin-channelsection-resource'),
        self::SECTION_CALL_TO_ACTION => Text::get('admin-channelsection-call_to_action'),
        self::SECTION_PROJECTS => Text::get('admin-channelsection-projects'),
        self::SECTION_POSTS => Text::get('admin-channelsection-posts'),
        self::SECTION_PROGRAM => Text::get('admin-channelsection-program'),
        self::SECTION_STORIES => Text::get('admin-channelsection-stories'),
        self::SECTION_WORKSHOPS => Text::get('admin-channelsection-workshops'),
        self::SECTION_TEAM => Text::get('admin-channelsection-team'),
        self::SECTION_SPONSORS => Text::get('admin-channelsection-sponsors'),
        self::SECTION_DATA_SETS => Text::get('admin-channelsection-data_sets'),
        self::SECTION_FOOTER => Text::get('admin-channelsection-footer')
      ];
    }
 }
