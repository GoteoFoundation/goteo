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
      ALTER TABLE `mail` ADD COLUMN `message_id` BIGINT UNSIGNED NULL AFTER `template`, ADD FOREIGN KEY (`message_id`) REFERENCES `message`(`id`) ON UPDATE CASCADE;

      UPDATE mail, (SELECT a.id AS mail_id, d.id AS message_id
        FROM mail a
        INNER JOIN `user` b ON b.email=a.email
        INNER JOIN `message_user` c ON c.user_id=b.id
        INNER JOIN `message` d ON d.id=c.message_id
          AND d.message LIKE CONCAT('%',a.subject,'%')
        WHERE a.content LIKE CONCAT('%',b.name,'%')) src
      SET mail.message_id = src.message_id
      WHERE mail.id = src.mail_id;
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
     ALTER TABLE `mail` DROP COLUMN `message_id`, DROP INDEX `message_id`, DROP FOREIGN KEY `mail_ibfk_3`;
     ";
  }

}
