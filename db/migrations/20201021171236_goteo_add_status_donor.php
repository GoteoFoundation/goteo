<?php
/**
 * Migration Task class.
 */

 use Goteo\Core\Model;

class GoteoAddStatusDonor
{
  public function preUp()
  {
      // add the pre-migration code here
  }

  public function postUp()
  {
      // add the post-migration code here

      $sql = "
        UPDATE `donor` SET status = 'pending', donor.pending = NOW() WHERE donor.year = :year;
        UPDATE `donor` SET completed = TIMESTAMP(processed), completed = NOW() WHERE confirmed AND processed IS NOT NULL AND processed != '0000-00-00' AND year = :year;
        UPDATE `donor` SET status = 'completed' WHERE amount != 0 AND donor.name != '' AND donor.name IS NOT NULL AND donor.nif != '' AND donor.nif IS NOT NULL AND donor.zipcode != '' AND donor.zipcode IS NOT NULL and year = :year;
      ";

      $values = [
          'year' => date('Y')
      ];

      Model::query($sql, $values);
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
        ALTER TABLE `donor` ADD COLUMN `status` VARCHAR(50),
                            ADD COLUMN `pending` DATETIME,
                            ADD COLUMN `completed` DATETIME,
                            ADD COLUMN `validated` DATETIME,
                            ADD COLUMN `declared` DATETIME;
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
        ALTER TABLE `donor` DROP COLUMN `status`,
                        DROP COLUMN `pending`,
                        DROP COLUMN `completed`,
                        DROP COLUMN `validated`,
                        DROP COLUMN `declared`;
        ";
  }

}