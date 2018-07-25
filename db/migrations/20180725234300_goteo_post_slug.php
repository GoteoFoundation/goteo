<?php

use Goteo\Model\Blog\Post;

/**
 * Migration Task class.
 */
class GoteoPostSlug
{
  public function preUp()
  {
      // add the pre-migration code here
      // Remove possible repeated index
      try {
        Post::query("ALTER TABLE IGNORE DROP INDEX `id`");
      } catch(\PDOException $e) {}
  }

  public function postUp()
  {
      // add the post-migration code here
      // Create default slugs for everybody
      foreach(Post::query("SELECT id,title FROM post")->fetchAll(\PDO::FETCH_OBJ) as $post) {
        $slug = Post::idealiza($post->title, false, false, 150);
        try {
            // If duplicate, let it null
            Post::query("UPDATE post SET slug=:slug WHERE id=:id", [':id' => $post->id, ':slug' => $slug]);
        } catch(\PDOException $e) {
            //
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
        ALTER TABLE `post` ADD COLUMN `slug` VARCHAR(150) NULL AFTER `blog`, ADD UNIQUE INDEX (`slug`);
        ;
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
        ALTER TABLE `post` DROP COLUMN `slug`, DROP INDEX `slug`;
     ";
  }

}
