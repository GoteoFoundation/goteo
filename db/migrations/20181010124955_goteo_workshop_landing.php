<?php
/**
 * Migration Task class.
 */
use Goteo\Core\Model;
class GoteoWorkshopLanding
{
  public function preUp()
  {
      // add the pre-migration code here
      if(!Model::query("SHOW TABLES LIKE 'workshop'")->rowCount()) {
        $sql = "CREATE TABLE `workshop`(
         `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
         `title` CHAR(255) NOT NULL,
         `description` TEXT NOT NULL,
         `date_in` DATE NOT NULL,
         `date_out` DATE NOT NULL,
         `schedule` CHAR(255) NOT NULL,
         `url` CHAR(255) NULL ,
         `call_id` VARCHAR(50),
         `modified` TIMESTAMP NOT NULL,
         PRIMARY KEY (`id`),
         FOREIGN KEY (`call_id`) REFERENCES `call`(`id`) ON UPDATE CASCADE
        ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Talleres';
        CREATE TABLE `workshop_lang` (
        `id` bigint(20) unsigned NOT NULL,
        `lang` varchar(2) NOT NULL,
        `title` CHAR(255) NOT NULL,
        `description` TEXT NOT NULL,
        `schedule` CHAR(255) NOT NULL,
        `pending` INT(1) NULL DEFAULT '0' COMMENT 'To be reviewed',
         UNIQUE KEY `id_lang` (`id`,`lang`),
         FOREIGN KEY (`id`) REFERENCES `workshop`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
        ";
        Model::query($sql);
      }
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
          `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          `workshop` BIGINT(20) UNSIGNED NOT NULL,
          `name` TINYTEXT NULL,
          `url` CHAR(255),
          `image` VARCHAR(255),
          `order` INT(11),
          PRIMARY KEY (`id`),

          CONSTRAINT `workshop_sponsor_ibfk_1` FOREIGN KEY (`workshop`) REFERENCES `workshop` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        );

      CREATE TABLE `workshop_sphere` (
          `workshop_id` BIGINT(20) UNSIGNED NOT NULL,
          `sphere_id` BIGINT(20) UNSIGNED NOT NULL,
          `order` INT(11),
           PRIMARY KEY (`workshop_id`, `sphere_id`),
            FOREIGN KEY (`workshop_id`) REFERENCES `workshop`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`sphere_id`) REFERENCES `sphere`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        );

       CREATE TABLE `workshop_stories` (
          `workshop_id` BIGINT(20) UNSIGNED NOT NULL,
          `stories_id` BIGINT(20) UNSIGNED NOT NULL,
          `order` INT(11),
           PRIMARY KEY (`workshop_id`, `stories_id`),
            FOREIGN KEY (`workshop_id`) REFERENCES `workshop`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`stories_id`) REFERENCES `stories`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
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
