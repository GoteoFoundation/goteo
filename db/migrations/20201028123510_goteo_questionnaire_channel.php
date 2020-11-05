<?php
/**
 * Migration Task class.
 */
class GoteoQuestionnaireChannel
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
        CREATE TABLE `questionnaire_channel` (
            `questionnaire` BIGINT(20) UNSIGNED NOT NULL,
            `channel` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
            PRIMARY KEY (`questionnaire`),
            CONSTRAINT `questionnaire_channel_ibfk` FOREIGN KEY (`questionnaire`) REFERENCES `questionnaire` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `questionnaire_channel_ibfk_2` FOREIGN KEY (`channel`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        );
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
        DROP TABLE `questionnaire_channel`;
     ";
  }

}