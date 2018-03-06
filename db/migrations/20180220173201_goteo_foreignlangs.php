<?php
/**
 * Migration Task class.
 */
class GoteoForeignlangs
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
        ALTER TABLE `user_translang` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        ALTER TABLE `user_review` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`review`) REFERENCES `review`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        ALTER TABLE `user_call` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        ALTER TABLE `glossary_lang` ADD FOREIGN KEY (`id`) REFERENCES `glossary`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        ALTER TABLE `banner_lang` ADD FOREIGN KEY (`id`) REFERENCES `banner`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        ALTER TABLE `open_tag_lang` ADD FOREIGN KEY (`id`) REFERENCES `open_tag`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        ALTER TABLE `project_open_tag` ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        ALTER TABLE `project_open_tag` CHANGE `open_tag` `open_tag` BIGINT(20) UNSIGNED NOT NULL, ADD FOREIGN KEY (`open_tag`) REFERENCES `open_tag`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
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
        ALTER TABLE `user_translang` DROP FOREIGN KEY `user_translang_ibfk_1`;
        ALTER TABLE `user_review` DROP FOREIGN KEY `user_review_ibfk_1`;
        ALTER TABLE `user_review` DROP FOREIGN KEY `user_review_ibfk_2`;
        ALTER TABLE `user_call` DROP FOREIGN KEY `user_call_ibfk_1`;
        ALTER TABLE `user_call` DROP FOREIGN KEY `user_call_ibfk_2`;
        ALTER TABLE `glossary_lang` DROP FOREIGN KEY `glossary_lang_ibfk_1`;
        ALTER TABLE `banner_lang` DROP FOREIGN KEY `banner_lang_ibfk_1`;
        ALTER TABLE `open_tag_lang` DROP FOREIGN KEY `open_tag_lang_ibfk_1`;
        ALTER TABLE `project_open_tag` DROP FOREIGN KEY `project_open_tag_ibfk_1`;
        ALTER TABLE `project_open_tag` DROP FOREIGN KEY `project_open_tag_ibfk_2`;
     ";
  }

}
