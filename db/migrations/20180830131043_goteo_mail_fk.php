<?php
/**
 * Migration Task class.
 */
class GoteoMailFk
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
     ALTER TABLE `mail` DROP FOREIGN KEY `mail_ibfk_3`;
     ALTER TABLE `mail` ADD CONSTRAINT `mail_ibfk_3` FOREIGN KEY (`message_id`) REFERENCES `message`(`id`) ON UPDATE CASCADE ON DELETE SET NULL;";
  }

  /**
   * Return the SQL statements for the Down migration
   *
   * @return string The SQL string to execute for the Down migration.
   */
  public function getDownSQL()
  {
     return "
        ALTER TABLE `mail` DROP FOREIGN KEY `mail_ibfk_3`;
        ALTER TABLE `mail` ADD CONSTRAINT `mail_ibfk_3` FOREIGN KEY (`message_id`) REFERENCES `message`(`id`) ON UPDATE CASCADE;
     ";
  }

}
