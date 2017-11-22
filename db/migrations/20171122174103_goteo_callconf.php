<?php
/**
 * Migration Task class.
 */
class GoteoCallconf
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
    return "ALTER TABLE `call_conf` CHANGE `limit2` `limit2` SET('normal','minimum','unlimited','none','fullunlimited') CHARSET utf8 COLLATE utf8_general_ci DEFAULT 'none' NOT NULL COMMENT 'tipo limite riego segunda ronda'; 
    ";
  }

  /**
   * Return the SQL statements for the Down migration
   *
   * @return string The SQL string to execute for the Down migration.
   */
  public function getDownSQL()
  {
     return "ALTER TABLE `call_conf` CHANGE `limit2` `limit2` SET('normal','minimum','unlimited','none') CHARSET utf8 COLLATE utf8_general_ci DEFAULT 'none' NOT NULL COMMENT 'tipo limite riego segunda ronda'";
  }

}