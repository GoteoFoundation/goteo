<?php
/**
 * Migration Task class.
 */
class GoteoForeigns
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
        DELETE FROM `comment` WHERE post NOT IN (SELECT id FROM post);
        ALTER TABLE `comment` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        ALTER TABLE `comment` ADD FOREIGN KEY (`post`) REFERENCES `post`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

        ALTER TABLE `invest` ADD FOREIGN KEY (`admin`) REFERENCES `user`(`id`) ON UPDATE CASCADE;

        UPDATE post SET author=SUBSTR(author, 2, CHAR_LENGTH(author) - 2 ) WHERE author NOT IN (SELECT id FROM `user`);
        ALTER TABLE `post` ADD FOREIGN KEY (`author`) REFERENCES `user`(`id`) ON UPDATE CASCADE;

        ALTER TABLE `mail` DROP FOREIGN KEY `mail_ibfk_2`;

        ALTER TABLE `mailer_send` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

        ALTER TABLE `patron` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

        DELETE FROM review_comment WHERE review NOT IN (SELECT id FROM `review`);
        ALTER TABLE `review_comment` ADD FOREIGN KEY (`review`) REFERENCES `review`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

        DELETE FROM review_score WHERE review NOT IN (SELECT id FROM `review`);
        ALTER TABLE `review_score` ADD FOREIGN KEY (`review`) REFERENCES `review`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`criteria`) REFERENCES `criteria`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

        ALTER TABLE `user_donation` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE;

        DELETE FROM user_favourite_project WHERE `project` NOT IN (SELECT id FROM `project`);
        ALTER TABLE `user_favourite_project` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

        ALTER TABLE `user_node` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`node`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

        DELETE FROM user_personal WHERE `user` NOT IN (SELECT id FROM `user`);
        ALTER TABLE `user_personal` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

        DELETE FROM user_prefer WHERE `user` NOT IN (SELECT id FROM `user`);
        ALTER TABLE `user_prefer` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

        ALTER TABLE `user_review` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

        ALTER TABLE `user_translate` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

        ALTER TABLE `user_vip` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
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

        ALTER TABLE `comment` DROP FOREIGN KEY `comment_ibfk_1`, DROP FOREIGN KEY `comment_ibfk_2`;
        ALTER TABLE `invest` DROP FOREIGN KEY `invest_ibfk_5`;
        ALTER TABLE `post` DROP FOREIGN KEY `post_ibfk_2`;
        ALTER TABLE `mail` ADD CONSTRAINT `mail_ibfk_2` FOREIGN KEY (`node`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        ALTER TABLE `mailer_send` DROP FOREIGN KEY `mailer_send_ibfk_2`;
        ALTER TABLE `patron` DROP FOREIGN KEY `patron_ibfk_3`;
        ALTER TABLE `review_comment` DROP FOREIGN KEY `review_comment_ibfk_1`, DROP FOREIGN KEY `review_comment_ibfk_2`;
        ALTER TABLE `review_score` DROP FOREIGN KEY `review_score_ibfk_1`, DROP FOREIGN KEY `review_score_ibfk_2`, DROP FOREIGN KEY `review_score_ibfk_3`;
        ALTER TABLE `user_donation` DROP FOREIGN KEY `user_donation_ibfk_1`;
        ALTER TABLE `user_favourite_project` DROP FOREIGN KEY `user_favourite_project_ibfk_1`, DROP FOREIGN KEY `user_favourite_project_ibfk_2`;
        ALTER TABLE `user_node` DROP FOREIGN KEY `user_node_ibfk_1`, DROP FOREIGN KEY `user_node_ibfk_2`;
        ALTER TABLE `user_personal` DROP FOREIGN KEY `user_personal_ibfk_1`;
        ALTER TABLE `user_prefer` DROP FOREIGN KEY `user_prefer_ibfk_1`;
        ALTER TABLE `user_review` DROP FOREIGN KEY `user_review_ibfk_1`;
        ALTER TABLE `user_translate` DROP FOREIGN KEY `user_translate_ibfk_1`;
        ALTER TABLE `user_vip` DROP FOREIGN KEY `user_vip_ibfk_1`;
     ";
  }

}
