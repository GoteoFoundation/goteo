<?php
/**
 * Migration Task class.
 */
class GoteoReworkQuestion
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
      ALTER TABLE `question_lang` DROP FOREIGN KEY `question_lang_ibfk_1`;
      ALTER TABLE `question_lang` CHANGE `question` `id` BIGINT(20) UNSIGNED NOT NULL;
      ALTER TABLE `question_lang` ADD CONSTRAINT `question_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `question` (`id`);
      ALTER TABLE `question_lang` ADD UNIQUE KEY `id_lang` (`id`,`lang`);
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
        ALTER TABLE `question_lang` DROP INDEX `id_lang`;
        ALTER TABLE `question_lang` DROP FOREIGN KEY `question_lang_ibfk_1`;
        ALTER TABLE `question_lang` CHANGE `id` `question` BIGINT UNSIGNED NOT NULL;
        ALTER TABLE `question_lang` ADD CONSTRAINT `question_lang_ibfk_1` FOREIGN KEY (`question`) REFERENCES `question` (`id`);
    ";
  }

}