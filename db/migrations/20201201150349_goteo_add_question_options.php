<?php
/**
 * Migration Task class.
 */
class GoteoAddQuestionOptions
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
        CREATE TABLE `question_options` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `question` BIGINT(20) UNSIGNED NOT NULL,
            `option` TEXT NOT NULL,
            `lang` varchar(3) NOT NULL,
            `order` INT(11),
            PRIMARY KEY (`id`),
            CONSTRAINT `question_options_ibfk_1` FOREIGN KEY (`question`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        );

        CREATE TABLE `question_options_lang` (
            `id` BIGINT(20) UNSIGNED NOT NULL,
            `option` TEXT NOT NULL,
            `lang` varchar(3) NOT NULL,
            CONSTRAINT `question_options_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `question_options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        );

        CREATE TABLE `question_answer_options` (
            `answer` BIGINT(20) UNSIGNED NOT NULL,
            `option` BIGINT(20) UNSIGNED NOT NULL,
            `other` TEXT NULL,
            `order` INT(11),
            UNIQUE KEY (`answer`, `option`),
            CONSTRAINT `question_answer_options_ibfk_1` FOREIGN KEY (`answer`) REFERENCES `question_answer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `question_answer_options_ibfk_2` FOREIGN KEY (`option`) REFERENCES `question_options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
        DROP TABLE `question_answer_options`;
        DROP TABLE `question_options_lang`;
        DROP TABLE `question_options`;
     ";
  }

}