<?php
/**
 * Migration Task class.
 */
class GoteoImpactItemLang
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
        ALTER TABLE `impact_data_lang` MODIFY `data_unit` VARCHAR(50);
        ALTER TABLE `impact_item` ADD COLUMN `lang` VARCHAR(6) NULL;

        CREATE TABLE `impact_item_lang` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(50) NOT NULL,
            `description` TEXT,
            `unit` VARCHAR(50) NOT NULL,
            `lang` VARCHAR(6) NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY (`id`,`lang`),
            CONSTRAINT `impact_item_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `impact_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
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
        DROP TABLE `impact_item_lang`;
        ALTER TABLE `impact_item` DROP COLUMN `lang`;
     ";
  }

}
