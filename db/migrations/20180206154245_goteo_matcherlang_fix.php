<?php
/**
 * Migration Task class.
 */
class GoteoMatcherlangFix
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
     // Due a bug in goteo_matcherlang.php file, try to recreeate the file if does not exists
     return "
      CREATE TABLE IF NOT EXISTS `matcher_lang` (
        `id` varchar(50) CHARACTER SET utf8 NOT NULL,
        `lang` varchar(2) CHARACTER SET utf8 DEFAULT NULL,
        `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `terms` longtext COLLATE utf8mb4_unicode_ci,
        PRIMARY KEY `id` (`id`),
        CONSTRAINT `matcher_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `matcher` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
      ALTER TABLE `matcher_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL, DROP PRIMARY KEY, ADD UNIQUE INDEX (`id`, `lang`);
     ";
  }

  /**
   * Return the SQL statements for the Down migration
   *
   * @return string The SQL string to execute for the Down migration.
   */
  public function getDownSQL()
  {
     return "ALTER TABLE `matcher_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NULL, ADD PRIMARY KEY (`id`), DROP INDEX `id`;";
  }

}
