<?php
/**
 * Migration Task class.
 */
class GoteoFilterSocialcommitment
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
         CREATE TABLE `filter_socialcommitment` (
                `filter` INT(11) NOT NULL,
                `social_commitment` INT(10) UNSIGNED NOT NULL,
                FOREIGN KEY (`filter`) REFERENCES `filter`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
                FOREIGN KEY (`social_commitment`) REFERENCES `social_commitment`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
                UNIQUE KEY `id_filter_socialcommitment` (`filter`,`social_commitment`)
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
        DROP TABLE `filter_socialcommitment`;
     ";
  }

}
