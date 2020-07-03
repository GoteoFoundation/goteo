<?php
/**
 * Migration Task class.
 */
class GoteoAddCreditsToImage
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
        CREATE TABLE `image_credits` (
            `id` VARCHAR(255) NOT NULL,
            `credits` VARCHAR(255) NOT NULL,
            `lang` VARCHAR(6) NULL,
             PRIMARY KEY (`id`)
          );

        CREATE TABLE `image_credits_lang` (
            `id` VARCHAR(255) NOT NULL,
            `credits` VARCHAR(255) NOT NULL,
            `lang` VARCHAR(6) NULL,
            FOREIGN KEY (`id`) REFERENCES `image_credits`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
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
        DROP TABLE `image_credits`;
     ";
  }

}