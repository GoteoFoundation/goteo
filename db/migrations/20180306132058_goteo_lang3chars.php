<?php
/**
 * Migration Task class.
 */
class GoteoLang3chars
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
    // Transform to allow ISO-631-2 (3 letters in lang tables)
     return "
        ALTER TABLE `banner_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `call_banner_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `call_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `category_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `cost_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `criteria_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `faq_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `glossary_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `icon_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `info_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `license_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `matcher_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `message_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `milestone_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `news_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `node_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `open_tag_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `page_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `post_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `project_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `promote_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `reward_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `social_commitment_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `sphere_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `stories_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `support_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `tag_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `template_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `user_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `worthcracy_lang` CHANGE `lang` `lang` VARCHAR(3) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
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
        ALTER TABLE `banner_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `call_banner_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `call_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `category_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `cost_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `criteria_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `faq_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `glossary_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `icon_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `info_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `license_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `matcher_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `message_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `milestone_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `news_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `node_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `open_tag_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `page_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `post_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `project_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `promote_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `reward_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `social_commitment_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `sphere_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `stories_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `support_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `tag_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `template_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `user_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `worthcracy_lang` CHANGE `lang` `lang` VARCHAR(2) CHARSET utf8 COLLATE utf8_general_ci NOT NULL;
     ";
  }

}
