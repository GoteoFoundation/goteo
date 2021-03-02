<?php
/**
 * Migration Task class.
 */
class GoteoQuestionConstraints
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
         ALTER TABLE `question_lang` ADD CONSTRAINT `question_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
        ALTER TABLE `question_lang` DROP FOREIGN KEY `question_lang_ibfk_1`;
        ALTER TABLE `question_lang` ADD CONSTRAINT `question_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `question` (`id`);
     ";
  }

}