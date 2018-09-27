<?php
/**
 * Migration Task class.
 */
class GoteoWorkshopLanding
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
      ALTER TABLE `workshop` ADD COLUMN `subtitle` TINYTEXT NULL AFTER `title`;
      ALTER TABLE `workshop` ADD COLUMN `type` TINYTEXT NULL AFTER `subtitle`;
      ALTER TABLE `workshop` ADD COLUMN `header_image` VARCHAR(255) NULL AFTER `url`;
      ALTER TABLE `workshop` ADD COLUMN `schedule_file_url` TINYTEXT NULL AFTER `header_image`;
      ALTER TABLE `workshop` ADD COLUMN `venue` TINYTEXT NULL AFTER `header_image`;
      ALTER TABLE `workshop` ADD COLUMN `city` TINYTEXT NULL AFTER `venue`;
      ALTER TABLE `workshop` ADD COLUMN `venue_address` TEXT NULL AFTER `city`;
      ALTER TABLE `workshop` ADD COLUMN `how_to_get` TINYTEXT NULL AFTER `venue_address`;
      ALTER TABLE `workshop` ADD COLUMN `map_iframe` TEXT NULL AFTER `how_to_get`;
      ALTER TABLE `workshop_lang` ADD COLUMN `subtitle` TINYTEXT NULL AFTER `title`;
      ALTER TABLE `workshop_lang` ADD COLUMN `how_to_get` TINYTEXT NULL AFTER `subtitle`;

      CREATE TABLE `workshop_sponsor` (
          `id` BIGINT(20) UNSIGNED NOT NULL,
          `workshop` BIGINT(20) UNSIGNED NOT NULL,
          `name` TINYTEXT NULL,
          `url` CHAR(255),
          `image` VARCHAR(255),
          `order` INT(11),
          PRIMARY KEY (`id`),

          CONSTRAINT `workshop_sponsor_ibfk_1` FOREIGN KEY (`workshop`) REFERENCES `workshop` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        );

      CREATE TABLE `workshop_sphere` (
          `workshop` BIGINT(20) UNSIGNED NOT NULL,
          `sphere` BIGINT(20) UNSIGNED NOT NULL,
          `order` INT(11),
           UNIQUE INDEX (`workshop`, `sphere`),
            FOREIGN KEY (`workshop`) REFERENCES `workshop`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`sphere`) REFERENCES `sphere`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        );

       CREATE TABLE `workshop_stories` (
          `workshop` BIGINT(20) UNSIGNED NOT NULL,
          `stories` BIGINT(20) UNSIGNED NOT NULL,
          `order` INT(11),
           UNIQUE INDEX (`workshop`, `stories`),
            FOREIGN KEY (`workshop`) REFERENCES `workshop`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`stories`) REFERENCES `stories`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
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
      ALTER TABLE `workshop` DROP COLUMN `subtitle`;
      ALTER TABLE `workshop` DROP COLUMN `type`;
      ALTER TABLE `workshop` DROP COLUMN `header_image`;
      ALTER TABLE `workshop` DROP COLUMN `schedule_file`;
      ALTER TABLE `workshop` DROP COLUMN `venue`;
      ALTER TABLE `workshop` DROP COLUMN `city`;
      ALTER TABLE `workshop` DROP COLUMN `venue_address`;
      ALTER TABLE `workshop` DROP COLUMN `map_iframe`;
      ALTER TABLE `workshop` DROP COLUMN `how_to_get`;
      ALTER TABLE `workshop_lang` DROP COLUMN `subtitle`;
      ALTER TABLE `workshop_lang` DROP COLUMN `how_to_get`;
      DROP TABLE `workshop_sponsor`;
      DROP TABLE `workshop_sphere`;
      DROP TABLE `workshop_stories`;
     ";
  }

}