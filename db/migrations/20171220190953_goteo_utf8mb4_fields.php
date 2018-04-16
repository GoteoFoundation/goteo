<?php
/**
 * Migration Task class.
 */
class GoteoUtf8mb4Fields
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
    ALTER TABLE `call` CHANGE `contract_name` `contract_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call` CHANGE `entity_office` `entity_office` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call` CHANGE `entity_name` `entity_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `conf` CHANGE `value` `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `document` CHANGE `name` `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `matcher` CHANGE `name` `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `matcher_lang` CHANGE `name` `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `milestone_lang` CHANGE `name` `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `node` CHANGE `name` `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `node` CHANGE `label` `label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `contract_name` `contract_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `entity_office` `entity_office` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `entity_name` `entity_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `schema_version` CHANGE `version` `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `sphere` CHANGE `name` `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `user` CHANGE `password` `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `user` CHANGE `avatar` `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `user_donation` CHANGE `name` `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `user_donation` CHANGE `surname` `surname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `user_donation` CHANGE `region` `region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `user_donation` CHANGE `countryname` `countryname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `user_personal` CHANGE `contract_name` `contract_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `user_personal` CHANGE `contract_surname` `contract_surname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `banner` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `banner` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `banner_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `banner_lang` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call` CHANGE `subtitle` `subtitle` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call` CHANGE `address` `address` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call` CHANGE `description` `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call` CHANGE `description_summary` `description_summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call` CHANGE `description_nav` `description_nav` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call` CHANGE `whom` `whom` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call` CHANGE `apply` `apply` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call` CHANGE `legal` `legal` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call` CHANGE `dossier` `dossier` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call` CHANGE `tweet` `tweet` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call` CHANGE `fbappid` `fbappid` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call` CHANGE `resources` `resources` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call` CHANGE `post_address` `post_address` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call_banner` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call_banner_lang` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call_lang` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call_lang` CHANGE `description` `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call_lang` CHANGE `description_summary` `description_summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call_lang` CHANGE `description_nav` `description_nav` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call_lang` CHANGE `whom` `whom` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call_lang` CHANGE `apply` `apply` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call_lang` CHANGE `legal` `legal` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call_lang` CHANGE `subtitle` `subtitle` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call_lang` CHANGE `dossier` `dossier` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call_lang` CHANGE `tweet` `tweet` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call_lang` CHANGE `resources` `resources` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `call_sponsor` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `category` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `category` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `category_lang` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `category_lang` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `comment` CHANGE `text` `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `cost` CHANGE `cost` `cost` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `cost` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `cost_lang` CHANGE `cost` `cost` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `cost_lang` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `criteria` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `criteria` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `criteria_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `criteria_lang` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `faq` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `faq` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `faq_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `faq_lang` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `feed` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `feed` CHANGE `html` `html` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `glossary` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `glossary` CHANGE `text` `text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `glossary` CHANGE `legend` `legend` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `glossary_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `glossary_lang` CHANGE `text` `text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `glossary_lang` CHANGE `legend` `legend` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `icon` CHANGE `description` `description` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `icon_lang` CHANGE `description` `description` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `info` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `info` CHANGE `text` `text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `info` CHANGE `legend` `legend` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `info` CHANGE `share_facebook` `share_facebook` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `info` CHANGE `share_twitter` `share_twitter` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `info_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `info_lang` CHANGE `text` `text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `info_lang` CHANGE `legend` `legend` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `info_lang` CHANGE `share_facebook` `share_facebook` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `info_lang` CHANGE `share_twitter` `share_twitter` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `invest_detail` CHANGE `log` `log` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `invest_msg` CHANGE `msg` `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `license` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `license_lang` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `log` CHANGE `text` `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `mail` CHANGE `content` `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `mail` CHANGE `error` `error` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `mailer_content` CHANGE `reply_name` `reply_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `mailer_send` CHANGE `error` `error` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `matcher` CHANGE `terms` `terms` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `matcher` CHANGE `vars` `vars` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `matcher_lang` CHANGE `terms` `terms` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `message` CHANGE `message` `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `message_lang` CHANGE `message` `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `milestone` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `milestone` CHANGE `twitter_msg` `twitter_msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `milestone` CHANGE `facebook_msg` `facebook_msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `milestone` CHANGE `twitter_msg_owner` `twitter_msg_owner` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `milestone` CHANGE `facebook_msg_owner` `facebook_msg_owner` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `milestone_lang` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `milestone_lang` CHANGE `twitter_msg` `twitter_msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `milestone_lang` CHANGE `facebook_msg` `facebook_msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `milestone_lang` CHANGE `twitter_msg_owner` `twitter_msg_owner` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `milestone_lang` CHANGE `facebook_msg_owner` `facebook_msg_owner` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `news` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `news` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `news` CHANGE `media_name` `media_name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `news_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `news_lang` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `node` CHANGE `subtitle` `subtitle` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `node` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `node_lang` CHANGE `subtitle` `subtitle` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `node_lang` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `open_tag` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `open_tag` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `open_tag_lang` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `open_tag_lang` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `page` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `page` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `page` CHANGE `content` `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `page_lang` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `page_lang` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `page_lang` CHANGE `content` `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `post` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `post` CHANGE `text` `text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `post` CHANGE `legend` `legend` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `post_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `post_lang` CHANGE `text` `text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `post_lang` CHANGE `legend` `legend` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `subtitle` `subtitle` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `address` `address` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `motivation` `motivation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `about` `about` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `goal` `goal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `related` `related` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `spread` `spread` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `reward` `reward` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `keywords` `keywords` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `resource` `resource` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `comment` `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `post_address` `post_address` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `social_commitment_description` `social_commitment_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `execution_plan` `execution_plan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project` CHANGE `sustainability_model` `sustainability_model` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project_data` CHANGE `comment` `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project_lang` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project_lang` CHANGE `motivation` `motivation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project_lang` CHANGE `about` `about` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project_lang` CHANGE `goal` `goal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project_lang` CHANGE `related` `related` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project_lang` CHANGE `reward` `reward` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project_lang` CHANGE `keywords` `keywords` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project_lang` CHANGE `subtitle` `subtitle` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `project_lang` CHANGE `social_commitment_description` `social_commitment_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `promote` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `promote` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `promote_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `promote_lang` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `review` CHANGE `to_checker` `to_checker` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `review` CHANGE `to_owner` `to_owner` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `review_comment` CHANGE `evaluation` `evaluation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `review_comment` CHANGE `recommendation` `recommendation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `reward` CHANGE `reward` `reward` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `reward` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `reward` CHANGE `other` `other` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `reward_lang` CHANGE `reward` `reward` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `reward_lang` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `reward_lang` CHANGE `other` `other` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `social_commitment` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `social_commitment_lang` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `sphere_lang` CHANGE `name` `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `sponsor` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `stories` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `stories` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `stories` CHANGE `review` `review` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `stories_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `stories_lang` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `stories_lang` CHANGE `review` `review` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `support` CHANGE `support` `support` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `support` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `support_lang` CHANGE `support` `support` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `support_lang` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `tag` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `tag_lang` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `template` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `template` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `template` CHANGE `text` `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `template_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `template_lang` CHANGE `text` `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `text` CHANGE `text` `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `user` CHANGE `about` `about` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `user` CHANGE `keywords` `keywords` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `user` CHANGE `contribution` `contribution` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `user_donation` CHANGE `address` `address` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `user_lang` CHANGE `about` `about` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `user_lang` CHANGE `keywords` `keywords` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `user_lang` CHANGE `contribution` `contribution` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `user_personal` CHANGE `address` `address` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `worthcracy` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ALTER TABLE `worthcracy_lang` CHANGE `name` `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
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
    ALTER TABLE `call` CHANGE `contract_name` `contract_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call` CHANGE `entity_office` `entity_office` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call` CHANGE `entity_name` `entity_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `conf` CHANGE `value` `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `document` CHANGE `name` `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `matcher` CHANGE `name` `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `matcher_lang` CHANGE `name` `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `milestone_lang` CHANGE `name` `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `node` CHANGE `name` `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `node` CHANGE `label` `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `contract_name` `contract_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `entity_office` `entity_office` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `entity_name` `entity_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `schema_version` CHANGE `version` `version` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `sphere` CHANGE `name` `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `user` CHANGE `password` `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `user` CHANGE `avatar` `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `user_donation` CHANGE `name` `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `user_donation` CHANGE `surname` `surname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `user_donation` CHANGE `region` `region` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `user_donation` CHANGE `countryname` `countryname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `user_personal` CHANGE `contract_name` `contract_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `user_personal` CHANGE `contract_surname` `contract_surname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `banner` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `banner` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `banner_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `banner_lang` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call` CHANGE `subtitle` `subtitle` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call` CHANGE `address` `address` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call` CHANGE `description` `description` longtext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call` CHANGE `description_summary` `description_summary` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call` CHANGE `description_nav` `description_nav` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call` CHANGE `whom` `whom` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call` CHANGE `apply` `apply` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call` CHANGE `legal` `legal` longtext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call` CHANGE `dossier` `dossier` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call` CHANGE `tweet` `tweet` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call` CHANGE `fbappid` `fbappid` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call` CHANGE `resources` `resources` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call` CHANGE `post_address` `post_address` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call_banner` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call_banner_lang` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call_lang` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call_lang` CHANGE `description` `description` longtext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call_lang` CHANGE `description_summary` `description_summary` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call_lang` CHANGE `description_nav` `description_nav` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call_lang` CHANGE `whom` `whom` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call_lang` CHANGE `apply` `apply` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call_lang` CHANGE `legal` `legal` longtext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call_lang` CHANGE `subtitle` `subtitle` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call_lang` CHANGE `dossier` `dossier` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call_lang` CHANGE `tweet` `tweet` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call_lang` CHANGE `resources` `resources` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `call_sponsor` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `category` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `category` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `category_lang` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `category_lang` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `comment` CHANGE `text` `text` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `cost` CHANGE `cost` `cost` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `cost` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `cost_lang` CHANGE `cost` `cost` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `cost_lang` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `criteria` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `criteria` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `criteria_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `criteria_lang` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `faq` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `faq` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `faq_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `faq_lang` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `feed` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `feed` CHANGE `html` `html` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `glossary` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `glossary` CHANGE `text` `text` longtext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `glossary` CHANGE `legend` `legend` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `glossary_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `glossary_lang` CHANGE `text` `text` longtext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `glossary_lang` CHANGE `legend` `legend` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `icon` CHANGE `description` `description` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `icon_lang` CHANGE `description` `description` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `info` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `info` CHANGE `text` `text` longtext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `info` CHANGE `legend` `legend` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `info` CHANGE `share_facebook` `share_facebook` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `info` CHANGE `share_twitter` `share_twitter` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `info_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `info_lang` CHANGE `text` `text` longtext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `info_lang` CHANGE `legend` `legend` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `info_lang` CHANGE `share_facebook` `share_facebook` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `info_lang` CHANGE `share_twitter` `share_twitter` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `invest_detail` CHANGE `log` `log` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `invest_msg` CHANGE `msg` `msg` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `license` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `license_lang` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `log` CHANGE `text` `text` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `mail` CHANGE `content` `content` longtext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `mail` CHANGE `error` `error` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `mailer_content` CHANGE `reply_name` `reply_name` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `mailer_send` CHANGE `error` `error` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `matcher` CHANGE `terms` `terms` longtext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `matcher` CHANGE `vars` `vars` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `matcher_lang` CHANGE `terms` `terms` longtext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `message` CHANGE `message` `message` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `message_lang` CHANGE `message` `message` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `milestone` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `milestone` CHANGE `twitter_msg` `twitter_msg` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `milestone` CHANGE `facebook_msg` `facebook_msg` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `milestone` CHANGE `twitter_msg_owner` `twitter_msg_owner` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `milestone` CHANGE `facebook_msg_owner` `facebook_msg_owner` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `milestone_lang` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `milestone_lang` CHANGE `twitter_msg` `twitter_msg` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `milestone_lang` CHANGE `facebook_msg` `facebook_msg` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `milestone_lang` CHANGE `twitter_msg_owner` `twitter_msg_owner` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `milestone_lang` CHANGE `facebook_msg_owner` `facebook_msg_owner` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `news` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `news` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `news` CHANGE `media_name` `media_name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `news_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `news_lang` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `node` CHANGE `subtitle` `subtitle` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `node` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `node_lang` CHANGE `subtitle` `subtitle` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `node_lang` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `open_tag` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `open_tag` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `open_tag_lang` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `open_tag_lang` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `page` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `page` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `page` CHANGE `content` `content` longtext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `page_lang` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `page_lang` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `page_lang` CHANGE `content` `content` longtext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `post` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `post` CHANGE `text` `text` longtext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `post` CHANGE `legend` `legend` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `post_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `post_lang` CHANGE `text` `text` longtext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `post_lang` CHANGE `legend` `legend` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `subtitle` `subtitle` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `address` `address` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `motivation` `motivation` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `about` `about` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `goal` `goal` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `related` `related` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `spread` `spread` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `reward` `reward` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `keywords` `keywords` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `resource` `resource` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `comment` `comment` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `post_address` `post_address` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `social_commitment_description` `social_commitment_description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `execution_plan` `execution_plan` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project` CHANGE `sustainability_model` `sustainability_model` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project_data` CHANGE `comment` `comment` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project_lang` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project_lang` CHANGE `motivation` `motivation` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project_lang` CHANGE `about` `about` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project_lang` CHANGE `goal` `goal` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project_lang` CHANGE `related` `related` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project_lang` CHANGE `reward` `reward` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project_lang` CHANGE `keywords` `keywords` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project_lang` CHANGE `subtitle` `subtitle` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `project_lang` CHANGE `social_commitment_description` `social_commitment_description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `promote` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `promote` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `promote_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `promote_lang` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `review` CHANGE `to_checker` `to_checker` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `review` CHANGE `to_owner` `to_owner` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `review_comment` CHANGE `evaluation` `evaluation` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `review_comment` CHANGE `recommendation` `recommendation` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `reward` CHANGE `reward` `reward` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `reward` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `reward` CHANGE `other` `other` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `reward_lang` CHANGE `reward` `reward` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `reward_lang` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `reward_lang` CHANGE `other` `other` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `social_commitment` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `social_commitment_lang` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `sphere_lang` CHANGE `name` `name` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `sponsor` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `stories` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `stories` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `stories` CHANGE `review` `review` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `stories_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `stories_lang` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `stories_lang` CHANGE `review` `review` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `support` CHANGE `support` `support` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `support` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `support_lang` CHANGE `support` `support` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `support_lang` CHANGE `description` `description` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `tag` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `tag_lang` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `template` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `template` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `template` CHANGE `text` `text` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `template_lang` CHANGE `title` `title` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `template_lang` CHANGE `text` `text` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `text` CHANGE `text` `text` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `user` CHANGE `about` `about` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `user` CHANGE `keywords` `keywords` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `user` CHANGE `contribution` `contribution` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `user_donation` CHANGE `address` `address` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `user_lang` CHANGE `about` `about` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `user_lang` CHANGE `keywords` `keywords` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `user_lang` CHANGE `contribution` `contribution` text CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `user_personal` CHANGE `address` `address` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `worthcracy` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
    ALTER TABLE `worthcracy_lang` CHANGE `name` `name` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci;
     ";
  }

}
