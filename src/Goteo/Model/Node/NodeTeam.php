<?php 

/**
 * Model for Node Team
 */

 namespace Goteo\Model\Node;

 use Goteo\Model\Image;
 use Goteo\Application\Exception\ModelNotFoundException;

 class NodeTeam extends \Goteo\Core\Model {

  protected $Table = 'node_team';
  protected static $Table_static = 'node_team';
  
  public
      $id,
      $name,
      $role,
      $image,
      $node_id;

  public static function get($id) {
    $sql = "SELECT 
                  node_team.*
            FROM node_team
            WHERE node_team.node_id = ?";

    $query = static::query($sql, array($id));
    $team = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);

    /*if (!$team)
      throw new ModelNotFoundException("Team not found");*/

    return $team;
  }

  public function getImage() {
    if (!$this->image instanceOf Image) {
      $this->image = new Image($this->image);
    }

    return $this->image;
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
        'role',
        'image',
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
      if (empty($this->id)) 
        $errors[] = "The team member has no name";

      return empty($errors);
    }


 }