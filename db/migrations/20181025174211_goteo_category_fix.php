<?php
/**
 * Migration Task class.
 */
class GoteoCategoryFix
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
      ALTER TABLE call_category DROP FOREIGN KEY call_category_ibfk_1;
      ALTER TABLE call_category ADD CONSTRAINT call_category_ibfk_1 FOREIGN KEY (`call`) REFERENCES `call`(id) ON UPDATE CASCADE;
      ALTER TABLE call_category DROP FOREIGN KEY call_category_ibfk_2;
      ALTER TABLE call_category ADD CONSTRAINT call_category_ibfk_2 FOREIGN KEY (category) REFERENCES category(id) ON UPDATE CASCADE;
      ALTER TABLE project_category DROP FOREIGN KEY project_category_ibfk_1;
      ALTER TABLE project_category ADD CONSTRAINT project_category_ibfk_1 FOREIGN KEY (project) REFERENCES project(id) ON UPDATE CASCADE;
      ALTER TABLE project_category DROP FOREIGN KEY project_category_ibfk_2;
      ALTER TABLE project_category ADD CONSTRAINT project_category_ibfk_2 FOREIGN KEY (category) REFERENCES category(id) ON UPDATE CASCADE;
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
      ALTER TABLE call_category DROP FOREIGN KEY call_category_ibfk_1;
      ALTER TABLE call_category ADD CONSTRAINT call_category_ibfk_1 FOREIGN KEY (`call`) REFERENCES `call`(id) ON UPDATE CASCADE ON DELETE CASCADE;
      ALTER TABLE call_category DROP FOREIGN KEY call_category_ibfk_2;
      ALTER TABLE call_category ADD CONSTRAINT call_category_ibfk_2 FOREIGN KEY (category) REFERENCES category(id) ON UPDATE CASCADE ON DELETE CASCADE;
      ALTER TABLE project_category DROP FOREIGN KEY project_category_ibfk_1;
      ALTER TABLE project_category ADD CONSTRAINT project_category_ibfk_1 FOREIGN KEY (project) REFERENCES project(id) ON UPDATE CASCADE ON DELETE CASCADE;
      ALTER TABLE project_category DROP FOREIGN KEY project_category_ibfk_2;
      ALTER TABLE project_category ADD CONSTRAINT project_category_ibfk_2 FOREIGN KEY (category) REFERENCES category(id) ON UPDATE CASCADE ON DELETE CASCADE;
     ";
  }

}
