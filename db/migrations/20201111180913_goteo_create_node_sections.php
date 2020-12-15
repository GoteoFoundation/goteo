<?php
/**
 * Migration Task class.
 */
class GoteoCreateNodeSections
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
        CREATE TABLE `node_sections` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `node` varchar(50) NOT NULL,
            `section` varchar(10) NOT NULL,
            `main_title` TEXT NULL,
            `main_description` TEXT  NULL,
            `main_image` VARCHAR(255) NULL,
            `main_button` VARCHAR(255) NULL,
            `lang` VARCHAR(6) NULL,
            `order` smallint(5) unsigned NOT NULL DEFAULT '1',
            PRIMARY KEY (`id`),
            UNIQUE (`node`,`section`),
            CONSTRAINT `node_section_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        CREATE TABLE `node_sections_lang` (
            `id` BIGINT(20) UNSIGNED NOT NULL,
            `main_title` TEXT NULL,
            `main_description` TEXT NULL,
            `main_button` VARCHAR(255) NULL,
            `lang` VARCHAR(6) NULL,
            CONSTRAINT `node_section_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `node_sections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
     DROP TABLE `node_sections_lang`;
     DROP TABLE `node_sections`;
     ";
  }

}