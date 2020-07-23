<?php
/**
 * Migration Task class.
 */
class GoteoAddUniqueKeyIdLang
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
        ALTER TABLE `call_to_action_lang` ADD UNIQUE KEY `id_lang` (`id`, `lang`);
        ALTER TABLE `node_program_lang` ADD UNIQUE KEY `id_lang` (`id`, `lang`);
        ALTER TABLE `node_faq_lang` ADD UNIQUE KEY `id_lang` (`id`, `lang`);
        ALTER TABLE `node_faq_question_lang` ADD UNIQUE KEY `id_lang` (`id`, `lang`);
        ALTER TABLE `node_faq_download_lang` ADD UNIQUE KEY `id_lang` (`id`, `lang`);
        ALTER TABLE `node_sponsor_lang` ADD UNIQUE KEY `id_lang` (`id`, `lang`);
        ALTER TABLE `node_team_lang` ADD UNIQUE KEY `id_lang` (`id`, `lang`);
        ALTER TABLE `node_resource_lang` ADD UNIQUE KEY `id_lang` (`id`, `lang`);
        ALTER TABLE `image_credits_lang` ADD UNIQUE KEY `id_lang` (`id`, `lang`);
        ALTER TABLE `question_lang` ADD UNIQUE KEY `question_lang` (`question`, `lang`);
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
        ALTER TABLE `call_to_action_lang` DROP KEY `id_lang`;
        ALTER TABLE `node_program_lang` DROP KEY `id_lang`;
        ALTER TABLE `node_faq_lang` DROP KEY `id_lang`;
        ALTER TABLE `node_faq_question_lang` DROP KEY `id_lang`;
        ALTER TABLE `node_faq_download_lang` DROP KEY `id_lang`;
        ALTER TABLE `node_sponsor_lang` DROP KEY `id_lang`;
        ALTER TABLE `node_team_lang` DROP KEY `id_lang`;
        ALTER TABLE `node_resource_lang` DROP KEY `id_lang`;
        ALTER TABLE `image_credits_lang` DROP KEY `id_lang`;
        ALTER TABLE `question_lang` DROP KEY `question_lang`;
     ";
  }

}