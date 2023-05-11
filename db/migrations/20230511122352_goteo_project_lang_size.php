<?php

use Goteo\Core\Model;

/**
 * Migration Task class.
 */
class GoteoProjectLangSize
{
  public function preUp()
  {
      // add the pre-migration code here
  }

  public function postUp()
  {
      // add the post-migration code here
  }

  public function preDown()
  {
        $sql = "
            UPDATE goteo.project SET lang = LEFT(lang, 2);
        ";

        Model::query($sql);
  }

  public function postDown()
  {
      // add the post-migration code here
  }

  /**
   * Return the SQL statements for the Up migration
   *
   * @return string The SQL string to execute for the Up migration.
   */
  public function getUpSQL()
  {
     return "
        ALTER TABLE project MODIFY COLUMN lang VARCHAR(3) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'es' NULL;
     ";
  }

  /**
   * Return the SQL statements for the Down migration
   *
   * @return string The SQL string to execute for the Down migration.
   */
  public function getDownSQL()
  {
     return "
        ALTER TABLE project MODIFY COLUMN lang VARCHAR(2) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'es' NULL;
     ";
  }

}
