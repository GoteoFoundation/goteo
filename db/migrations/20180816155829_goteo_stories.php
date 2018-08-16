<?php
/**
 * Migration Task class.
 */
class GoteoStories
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
        DELETE FROM stories_lang WHERE id NOT IN (SELECT id FROM stories);
        DELETE FROM sphere_lang WHERE id NOT IN (SELECT id FROM sphere);
        ALTER TABLE `stories_lang` ADD FOREIGN KEY (`id`) REFERENCES `stories`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        ALTER TABLE `sphere_lang` ADD FOREIGN KEY (`id`) REFERENCES `sphere`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        ALTER TABLE `stories` ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
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
        ALTER TABLE `stories_lang` DROP FOREIGN KEY `stories_lang_ibfk_1`;
        ALTER TABLE `sphere_lang` DROP FOREIGN KEY `sphere_lang_ibfk_1`;
        ALTER TABLE `stories` DROP FOREIGN KEY `stories_ibfk_2`;
     ";
  }

}
