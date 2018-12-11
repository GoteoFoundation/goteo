<?php
/**
 * Migration Task class.
 */
class GoteoSubjectMailChar
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
      // add the pre-migration code here
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
        ALTER TABLE `mail` CHANGE `subject` `subject` CHAR(255) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;
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
        ALTER TABLE `mail` CHANGE `subject` `subject` CHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL;
    ";
  }

}
