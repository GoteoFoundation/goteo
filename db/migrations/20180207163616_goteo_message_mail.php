<?php
/**
 * Migration Task class.
 */
class GoteoMessageMail
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
      ALTER TABLE `message` ADD COLUMN `mail_id` BIGINT UNSIGNED NULL AFTER `private`, ADD FOREIGN KEY (`mail_id`) REFERENCES `mail`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT;

      UPDATE message,
             (SELECT a.id AS mail_id, d.id AS message_id
              FROM mail a
              INNER JOIN `user` b ON b.email=a.email
              INNER JOIN `message_user` c ON c.user_id=b.id
              INNER JOIN `message` d ON d.id=c.message_id
                AND d.message LIKE CONCAT('%',a.subject,'%')
              WHERE a.content LIKE CONCAT('%',b.name,'%')) src
      SET message.mail_id = src.mail_id
      WHERE message.id = src.message_id;
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
     ALTER TABLE `message` DROP COLUMN `mail_id`, DROP INDEX `mail_id`, DROP FOREIGN KEY `message_ibfk_4`;
     ";
  }

}
