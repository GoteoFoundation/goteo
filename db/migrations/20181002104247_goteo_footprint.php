<?php
/**
 * Migration Task class.
 */
use Goteo\Model\SocialCommitment;
use Goteo\Model\Category;

class GoteoFootprint
{
  public function preUp()
  {
      // add the pre-migration code here
  }

  public function postUp()
  {
      // add the post-migration code here
    $socials = [
      '1' => [2], // Solidarity => (B)
      '2' => [2,3], // free software => B/C
      '3' => [2], // Employment => B
      '4' => [1,2,3], // From design =>  A/B/C
      '5' => [3], // Journalism => C
      '6' => [2], // Educative => B
      '7' => [2,3], // Cultural => B/C
      '8' => [1], // Ecological => A
      '9' => [1,2,3], // Investigation => A/B/C
      '10' => [3], // open data => C
      '11' => [3],  // Democratic values => C
      '12' => [3], // Citizen participation => C
      '13' => [3] // Gender => C
    ];
    foreach($socials as $id => $footprints) {
        if($sc = SocialCommitment::get($id)) {
            $sc->addFootprints($footprints);
        }
    }
    $categories = [
      2 => [2], // Social => (B)
      6 => [3], // Communicative => C
      7 => [2,3], // Technology => B/C
      9 => [2], // Entrepreneur => B
      10 => [2], // Educative => B
      11 => [2, 3], // Cultural => B/C
      13 => [1], // Ecological => A
      14 => [1, 2, 3], // Scientific => A/B/C
      16 => [1, 2, 3] // Design =>  A/B/C
    ];
    foreach($categories as $id => $footprints) {
        if($sc = Category::get($id)) {
            $sc->addFootprints($footprints);
        }
    }

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
         CREATE TABLE `social_commitment_footprint`(
            `footprint_id` INT(10) UNSIGNED NOT NULL,
            `social_commitment_id` INT(10) UNSIGNED NOT NULL,
            `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
            PRIMARY KEY (`footprint_id`, `social_commitment_id`),
            FOREIGN KEY (`footprint_id`) REFERENCES `footprint`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`social_commitment_id`) REFERENCES `social_commitment`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
            );
         CREATE TABLE `category_footprint`(
            `footprint_id` INT(10) UNSIGNED NOT NULL,
            `category_id` INT(10) UNSIGNED NOT NULL,
            `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
            PRIMARY KEY (`footprint_id`, `category_id`),
            FOREIGN KEY (`footprint_id`) REFERENCES `footprint`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`category_id`) REFERENCES `category`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
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
     DROP TABLE social_commitment_footprint;
     DROP TABLE category_footprint;
     ";
  }

}
