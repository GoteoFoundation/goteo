<?php
/**
 * Migration Task class.
 */
class GoteoCallchecks
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

      CREATE TABLE `call_check` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `call` varchar(50) NOT NULL,
        `lang` varchar(2) NOT NULL,
        `description` TEXT DEFAULT NULL,
        PRIMARY KEY ( `id` ),
        CONSTRAINT `call` FOREIGN KEY (`call`)
            REFERENCES `call`(`id`)
              ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Call check';

      CREATE TABLE `call_check_lang` (
        `id` int(10) unsigned NOT NULL,
        `lang` varchar(2) NOT NULL,
        `description` TEXT NOT NULL,
        `pending` INT(1) NULL DEFAULT '0' COMMENT 'To be reviewed',
         UNIQUE KEY `id_lang` (`id`,`lang`),
         FOREIGN KEY (`id`) REFERENCES `call_check`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

      CREATE TABLE `call_check_project`( `id` INT(10) NOT NULL AUTO_INCREMENT, `call_check` INT(10) UNSIGNED NOT NULL, `project` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL, `response` TEXT, PRIMARY KEY (`id`), CONSTRAINT `call_check` FOREIGN KEY (`call_check`) REFERENCES `call_check`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, CONSTRAINT `project` FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE );

      ALTER TABLE `call` ADD `intro_checks` TEXT NULL DEFAULT NULL COMMENT 'Intro checks in apply page';

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

      DROP TABLE `call_check_project`;
      DROP TABLE `call_check_lang`;
      DROP TABLE `call_check`;
      ALTER TABLE `call` DROP COLUMN `intro_checks`;

     ";
  }

}
