<?php
/**
 * Migration Task class.
 */
class GoteoChannelCall
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
        ALTER TABLE `node` ADD COLUMN type VARCHAR(255) DEFAULT 'normal' AFTER `active`;
        ALTER TABLE `node` ADD COLUMN call_inscription_open INT(1) DEFAULT 1 AFTER project_creation_open;
        ALTER TABLE `node` ADD COLUMN banner_header_image VARCHAR(255) NULL AFTER call_inscription_open;
        ALTER TABLE `node` ADD COLUMN banner_header_image_md VARCHAR(255) NULL AFTER banner_header_image;
        ALTER TABLE `node` ADD COLUMN banner_header_image_sm VARCHAR(255) NULL AFTER banner_header_image_md;
        ALTER TABLE `node` ADD COLUMN banner_header_image_xs VARCHAR(255) NULL AFTER banner_header_image_sm;
        ALTER TABLE `node` ADD COLUMN main_info_title VARCHAR(255) NULL AFTER description;
        ALTER TABLE `node` ADD COLUMN main_info_description TEXT NULL AFTER main_info_title;
        ALTER TABLE `node` ADD COLUMN terms_banner_title VARCHAR(255) NULL;
        ALTER TABLE `node` ADD COLUMN terms_banner_description TEXT NULL;
        ALTER TABLE `node` ADD COLUMN terms_download_title VARCHAR(255)  NULL;
        ALTER TABLE `node` ADD COLUMN terms_download_description TEXT NULL;
        ALTER TABLE `node` ADD COLUMN terms_download_url VARCHAR(255)  NULL;

        ALTER TABLE `node_lang` ADD COLUMN `main_info_title` VARCHAR(255) NULL AFTER description;
        ALTER TABLE `node_lang` ADD COLUMN main_info_description TEXT NULL AFTER main_info_title;

        ALTER TABLE `node_sponsor` ADD COLUMN `label` TINYTEXT CHARSET utf8 COLLATE utf8_general_ci NULL AFTER `image`;

        CREATE TABLE `node_program` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `node_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `header` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `icon` varchar(255) NULL,
            `description` TEXT NOT NULL,
            `action` VARCHAR(255) NOT NULL,
            `action_url` TINYTEXT NULL,
            `lang` VARCHAR(6) NULL,
            `date` date NOT NULL,
            `order` INT(11),
             PRIMARY KEY (`id`),
             CONSTRAINT `node_program_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
          );
  
        CREATE TABLE `node_program_lang` (
          `id` BIGINT(20) UNSIGNED NOT NULL,
          `lang` VARCHAR (6),
          `title` VARCHAR(255) NOT NULL,
          `description` TEXT NOT NULL,
          `action` VARCHAR(255) NOT NULL,
          `action_url` TINYTEXT NULL,
          `pending` TINYINT (1),
           FOREIGN KEY (`id`) REFERENCES `node_program`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        );

        CREATE TABLE `node_faq` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `node_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
            `type` VARCHAR(255) DEFAULT 'general',
            `title` VARCHAR(255) NOT NULL,
            `icon` varchar(255) NULL,
            `description` TEXT NOT NULL,
            `lang` VARCHAR(6) NULL,
            `order` INT(11),
             PRIMARY KEY (`id`),
             CONSTRAINT `node_faq_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
          );

         CREATE TABLE `node_faq_lang` (
            `id` BIGINT(20) UNSIGNED NOT NULL,
            `lang` VARCHAR (6),
            `title` VARCHAR(255) NOT NULL,
            `description` TEXT NOT NULL,
            `pending` TINYINT (1),
             FOREIGN KEY (`id`) REFERENCES `node_faq`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        ); 

        CREATE TABLE `node_faq_type` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `node_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `banner_title` VARCHAR(255) NOT NULL,
            `banner_description` TEXT NOT NULL,
            `banner_header_image` VARCHAR(255) NULL,
            `banner_header_image_md` VARCHAR(255) NULL,
            `banner_header_image_sm` VARCHAR(255) NULL,
            `banner_header_image_xs` VARCHAR(255) NULL,
            `lang` VARCHAR(6) NULL,
             PRIMARY KEY (`id`), 
             CONSTRAINT `node_faq_type_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
          );

         CREATE TABLE `node_faq_type_lang` (
            `id` BIGINT(20) UNSIGNED NOT NULL,
            `lang` VARCHAR (6),
            `name` VARCHAR(255) NOT NULL,
            `banner_title` VARCHAR(255) NOT NULL,
            `banner_description` TEXT NOT NULL,
            `pending` TINYINT (1),
             FOREIGN KEY (`id`) REFERENCES `node_faq_type`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        ); 

        CREATE TABLE `node_faq_download` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `node_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
            `type` VARCHAR(255) DEFAULT 'general',
            `title` VARCHAR(255) NOT NULL,
            `icon` varchar(255) NULL,
            `description` TEXT NOT NULL,
            `url` VARCHAR(255) NOT NULL,
            `lang` VARCHAR(6) NULL,
            `order` INT(11),
             PRIMARY KEY (`id`),
             CONSTRAINT `node_faq_download_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
          );

         CREATE TABLE `node_faq_download_lang` (
            `id` BIGINT(20) UNSIGNED NOT NULL,
            `lang` VARCHAR (6),
            `title` VARCHAR(255) NOT NULL,
            `description` TEXT NOT NULL,
            `pending` TINYINT (1),
             FOREIGN KEY (`id`) REFERENCES `node_faq_download`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        ); 

        CREATE TABLE `node_sponsor_lang` (
            `id` BIGINT(20) UNSIGNED NOT NULL,
            `lang` VARCHAR (6),
            `label` TINYTEXT CHARSET utf8 COLLATE utf8_general_ci,
            `pending` TINYINT (1),
             FOREIGN KEY (`id`) REFERENCES `node_sponsor`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        ); 
      

        CREATE TABLE `node_team` (
            `id` BIGINT(20) UNSIGNED NOT NULL,
            `node_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
            `lang` VARCHAR (6),
            `name` VARCHAR(100) NOT NULL,
            `role` VARCHAR(50) DEFAULT NULL,
            `image` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            PRIMARY KEY (`id`),
            CONSTRAINT `node_team_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        );

        CREATE TABLE `node_team_lang` (
            `id` BIGINT(20) UNSIGNED NOT NULL,
            `lang` VARCHAR (6),
            `role` VARCHAR(50) DEFAULT NULL,
             FOREIGN KEY (`id`) REFERENCES `node_team`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        );

        ALTER TABLE `node_lang` ADD COLUMN terms_banner_title VARCHAR(255) NULL;
        ALTER TABLE `node_lang` ADD COLUMN terms_banner_description TEXT NULL;
        ALTER TABLE `node_lang` ADD COLUMN terms_download_title VARCHAR(255)  NULL;
        ALTER TABLE `node_lang` ADD COLUMN terms_download_description TEXT NULL;
        ALTER TABLE `node_lang` ADD COLUMN terms_download_url VARCHAR(255)  NULL;

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
        ALTER TABLE `node` DROP COLUMN type;
        ALTER TABLE `node` DROP COLUMN call_inscription_open;
        ALTER TABLE `node` DROP COLUMN banner_header_image;
        ALTER TABLE `node` DROP COLUMN banner_header_image_md;
        ALTER TABLE `node` DROP COLUMN banner_header_image_sm;
        ALTER TABLE `node` DROP COLUMN banner_header_image_xs;
        ALTER TABLE `node` DROP COLUMN main_info_title;
        ALTER TABLE `node` DROP COLUMN main_info_description;
        ALTER TABLE `node` DROP COLUMN terms_banner_title;  
        ALTER TABLE `node` DROP COLUMN terms_banner_description;
        ALTER TABLE `node` DROP COLUMN terms_download_title;
        ALTER TABLE `node` DROP COLUMN terms_download_description;
        ALTER TABLE `node` DROP COLUMN terms_download_url;


        ALTER TABLE `node_lang` DROP COLUMN main_info_title;
        ALTER TABLE `node_lang` DROP COLUMN main_info_description;  
        ALTER TABLE `node_lang` DROP COLUMN terms_banner_title;  
        ALTER TABLE `node_lang` DROP COLUMN terms_banner_description;  
        ALTER TABLE `node_lang` DROP COLUMN terms_download_title;  
        ALTER TABLE `node_lang` DROP COLUMN terms_download_description;  
        ALTER TABLE `node_lang` DROP COLUMN terms_download_url;  

        ALTER TABLE `node_sponsor` DROP COLUMN label;

        DROP TABLE `node_program_lang`;
        DROP TABLE `node_program`;
        DROP TABLE `node_faq_lang`;
        DROP TABLE `node_faq`;
        DROP TABLE `node_sponsor_lang`;
        DROP TABLE `node_team`;
     ";
  }

}