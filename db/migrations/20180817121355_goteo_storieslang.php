<?php

use Goteo\Application\Config;

/**
 * Migration Task class.
 */
class GoteoStorieslang
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
    $lang = Config::get('sql_lang');
    return "
        ALTER TABLE `stories` ADD COLUMN `lang` VARCHAR(3) NOT NULL AFTER `project`;
        UPDATE stories a
            INNER JOIN project b ON a.project=b.id
            SET a.lang=b.lang;
        UPDATE stories SET lang='$lang' WHERE ISNULL(project);
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
        ALTER TABLE `stories` DROP COLUMN `lang`;
     ";
  }

}
