<?php
use Goteo\Application\Config;

/**
 * Migration Task class.
 */
class GoteoUtf8mb4
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
    $db = Config::get('db.database');
     return "
        ALTER DATABASE `$db` CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
        ALTER TABLE `banner` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `banner_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `blog` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `call` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `call_banner` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `call_banner_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `call_category` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `call_conf` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `call_icon` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `call_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `call_location` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `call_post` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `call_project` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `call_sphere` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `call_sponsor` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `campaign` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `category` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `category_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `comment` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `conf` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `contract` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `contract_status` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `cost` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `cost_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `criteria` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `criteria_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `document` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `event` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `faq` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `faq_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `feed` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `glossary` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `glossary_image` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `glossary_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `home` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `icon` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `icon_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `icon_license` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `image` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `info` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `info_image` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `info_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `invest` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `invest_address` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `invest_detail` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `invest_location` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `invest_msg` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `invest_node` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `invest_reward` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `license` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `license_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `log` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `mail` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `mail_stats` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `mail_stats_location` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `mailer_content` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `mailer_control` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `mailer_limit` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `mailer_send` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `matcher` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `matcher_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `matcher_project` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `matcher_user` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `message` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `message_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `message_user` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `metric` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `milestone` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `milestone_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `news` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `news_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `node` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `node_data` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `node_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `open_tag` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `open_tag_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `origin` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `page` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `page_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `post` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `post_image` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `post_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `post_node` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `post_tag` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `project` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `project_account` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `project_category` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `project_conf` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `project_data` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `project_image` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `project_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `project_location` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `project_milestone` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `project_open_tag` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `promote` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `promote_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `review` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `review_comment` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `review_score` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `reward` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `reward_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `role` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `schema_version` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `social_commitment` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `social_commitment_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `sphere` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `sphere_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `sponsor` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `stories` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `stories_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `support` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `support_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `tag` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `tag_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `template` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `template_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `text` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_api` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_call` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_donation` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_favourite_project` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_interest` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_location` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_login` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_node` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_personal` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_pool` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_prefer` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_project` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_review` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_role` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_translang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_translate` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_vip` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `user_web` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `worthcracy` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
        ALTER TABLE `worthcracy_lang` CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci;
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
        ALTER DATABASE $db CHARACTER SET = utf8 COLLATE = utf8_general_ci;
        ALTER TABLE `banner` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `banner_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `blog` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `call` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `call_banner` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `call_banner_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `call_category` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `call_conf` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `call_icon` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `call_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `call_location` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `call_post` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `call_project` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `call_sphere` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `call_sponsor` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `campaign` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `category` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `category_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `comment` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `conf` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `contract` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `contract_status` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `cost` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `cost_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `criteria` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `criteria_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `document` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `event` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `faq` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `faq_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `feed` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `glossary` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `glossary_image` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `glossary_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `home` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `icon` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `icon_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `icon_license` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `image` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `info` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `info_image` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `info_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `invest` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `invest_address` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `invest_detail` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `invest_location` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `invest_msg` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `invest_node` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `invest_reward` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `license` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `license_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `log` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `mail` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `mail_stats` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `mail_stats_location` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `mailer_content` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `mailer_control` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `mailer_limit` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `mailer_send` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `matcher` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `matcher_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `matcher_project` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `matcher_user` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `message` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `message_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `message_user` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `metric` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `milestone` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `milestone_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `news` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `news_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `node` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `node_data` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `node_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `open_tag` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `open_tag_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `origin` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `page` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `page_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `post` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `post_image` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `post_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `post_node` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `post_tag` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `project` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `project_account` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `project_category` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `project_conf` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `project_data` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `project_image` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `project_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `project_location` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `project_milestone` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `project_open_tag` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `promote` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `promote_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `review` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `review_comment` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `review_score` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `reward` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `reward_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `role` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `schema_version` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `social_commitment` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `social_commitment_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `sphere` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `sphere_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `sponsor` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `stories` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `stories_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `support` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `support_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `tag` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `tag_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `template` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `template_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `text` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_api` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_call` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_donation` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_favourite_project` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_interest` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_location` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_login` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_node` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_personal` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_pool` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_prefer` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_project` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_review` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_role` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_translang` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_translate` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_vip` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `user_web` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `worthcracy` CHARSET=utf8, COLLATE=utf8_general_ci;
        ALTER TABLE `worthcracy_lang` CHARSET=utf8, COLLATE=utf8_general_ci;
     ";
  }

}
