<?php
/**
 * Migration Task class.
 */
class GoteoQuestionnaire
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
     return "";
  }

  /**
   * Return the SQL statements for the Down migration
   *
   * @return string The SQL string to execute for the Down migration.
   */
  public function getDownSQL()
  {
     return "
        CREATE TABLE `questionnaire` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `matcher` VARCHAR(50) NOT NULL,
            `vars` text,
            PRIMARY KEY (`id`)
        );
        
        CREATE TABLE `questionnaire_answer` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `questionnaire` BIGINT(20) NOT NULL,
            `answer` text,
            CONSTRAINT `questionnaire_answer_ibfk_1` FOREIGN KEY (`questionnaire`) REFERENCES `questionnaire` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        );

        ALTER TABLE `document` ALTER COLUMN `contract` varchar(50) NULL;
    ";
  }

}