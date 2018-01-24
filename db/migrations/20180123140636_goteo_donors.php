<?php
/**
 * Migration Task class.
 */
class GoteoDonors
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
        UPDATE `donor` SET pdf='' WHERE ISNULL(pdf);
        UPDATE `donor` SET confirmed=0 WHERE ISNULL(confirmed);
        UPDATE `donor` SET edited=0 WHERE ISNULL(edited);
        ALTER TABLE `donor`
        CHANGE `edited` `edited` INT(1) DEFAULT 0 NOT NULL COMMENT 'Revisados por el usuario',
        CHANGE `confirmed` `confirmed` INT(1) DEFAULT 0 NOT NULL COMMENT 'Certificado generado',
        CHANGE `pdf` `pdf` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL COMMENT 'nombre del archivo de certificado',
        ADD COLUMN `processed` DATE NOT NULL COMMENT 'Si se ha presentado el certificado en hacienda' AFTER `pdf`;
        UPDATE `donor` SET `processed`=CONCAT(`year`+1,'-','01','-','01') WHERE `confirmed`=1 AND `pdf`!='' AND `year` < 2017;
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
        ALTER TABLE `donor` DROP COLUMN `processed`,
        CHANGE `edited` `edited` INT(1) DEFAULT 0 NULL COMMENT 'Revisados por el usuario',
        CHANGE `confirmed` `confirmed` INT(1) DEFAULT 0 NULL COMMENT 'Certificado generado',
        CHANGE `pdf` `pdf` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL COMMENT 'nombre del archivo de certificado';

     ";
  }

}
