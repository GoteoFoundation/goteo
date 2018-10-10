<?php
/**
 * Migration Task class.
 */
class GoteoProjectsocialcommitment
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
     UPDATE project SET  social_commitment=NULL WHERE social_commitment NOT IN (SELECT id FROM social_commitment);
     ALTER TABLE `project`
        CHANGE `social_commitment` `social_commitment` INT(10) UNSIGNED NULL COMMENT 'Social commitment of the project',
        ADD FOREIGN KEY (`social_commitment`) REFERENCES `social_commitment`(`id`) ON UPDATE CASCADE;
     ";
  }

  /**
   * Return the SQL statements for the Down migration
   *
   * @return string The SQL string to execute for the Down migration.
   */
  public function getDownSQL()
  {
     return "ALTER TABLE `project`
        CHANGE `social_commitment` `social_commitment` VARCHAR(50) NULL COMMENT 'Social commitment of the project',
        DROP FOREIGN KEY `project_ibfk_3`; ";
  }

}
