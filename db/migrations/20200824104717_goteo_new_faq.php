<?php
/**
 * Migration Task class.
 */
class GoteoNewFaq
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

      CREATE TABLE `faq_section` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `slug` VARCHAR(150) NOT NULL,
            `icon` varchar(255) NULL,
            `banner_header` VARCHAR(255) NULL,
            `button_action` VARCHAR(255) NOT NULL,
            `button_url` VARCHAR(255) NOT NULL,
            `lang` VARCHAR(6) NULL,
            `order` INT(11),
             PRIMARY KEY (`id`) 
      );

      CREATE TABLE `faq_section_lang` (
            `id` BIGINT(20) UNSIGNED NOT NULL,
            `lang` VARCHAR (6),
            `name` VARCHAR(255) NOT NULL,
            `button_action` VARCHAR(255) NOT NULL,
            `button_url` VARCHAR(255) NOT NULL,
            `pending` TINYINT (1),
             FOREIGN KEY (`id`) REFERENCES `faq_section`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
      );

      CREATE TABLE `faq_subsection` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `section_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `slug` VARCHAR(150) NOT NULL,
            `lang` VARCHAR(6) NULL,
            `order` INT(11),
             PRIMARY KEY (`id`)
      );

      CREATE TABLE `faq_subsection_lang` (
            `id` BIGINT(20) UNSIGNED NOT NULL,
            `lang` VARCHAR (6),
            `name` VARCHAR(255) NOT NULL,
            `pending` TINYINT (1),
             FOREIGN KEY (`id`) REFERENCES `faq_subsection`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
      ); 

      ALTER TABLE `faq` ADD COLUMN `subsection_id` BIGINT(20) UNSIGNED NOT NULL;

      ALTER TABLE `faq` ADD CONSTRAINT `faq_ibfk_2` FOREIGN KEY (`subsection_id`) REFERENCES `faq_subsection`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

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
       DROP TABLE `faq_subsection_lang`;
       DROP TABLE `faq_subsection`;
       DROP TABLE `faq_section_lang`;
       DROP TABLE `faq_section`;
       ALTER TABLE `faq` DROP COLUMN `subsection_id`;

     ";
  }

}