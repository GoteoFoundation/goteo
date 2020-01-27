<?php
/**
 * Migration Task class.
 */
class GoteoQuestionnaire
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

      CREATE TABLE `questionnaire` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `matcher` VARCHAR(50) NOT NULL,
            `lang` varchar(3) NOT NULL,
            PRIMARY KEY (`id`)
        );

      CREATE TABLE `question` (
          `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          `questionnaire` BIGINT(20) UNSIGNED NOT NULL,
          `lang` varchar(3) NOT NULL,
          `title` text,
          `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
          `vars` text,
          PRIMARY KEY (`id`),
          CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`questionnaire`) REFERENCES `questionnaire` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
      );
        
      CREATE TABLE `question_lang` (
          `question` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          `lang` varchar(3) NOT NULL,
          `title` text,
          CONSTRAINT `question_lang_ibfk_1` FOREIGN KEY (`question`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
      );

      CREATE TABLE `questionnaire_answer` (
          `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          `questionnaire` BIGINT(20) UNSIGNED NOT NULL,
          `project` VARCHAR(50) NOT NULL COLLATE utf8_general_ci,
          PRIMARY KEY (`id`),
          CONSTRAINT `questionnaire_answer_project` FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
          CONSTRAINT `questionnaire_answer_ibfk_1` FOREIGN KEY (`questionnaire`) REFERENCES `questionnaire` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
      );

      CREATE TABLE `question_answer` (
        `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `questionnaire_answer` BIGINT(20) UNSIGNED NOT NULL,
        `question` BIGINT(20) UNSIGNED NOT NULL,
        `answer` TEXT,
        PRIMARY KEY (`id`),
        CONSTRAINT `question_answer_answer` FOREIGN KEY (`questionnaire_answer`) REFERENCES `questionnaire_answer` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `question_answer_question` FOREIGN KEY (`question`) REFERENCES `question` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
      );

      ALTER TABLE `document` CHANGE `contract` `contract` VARCHAR(50) NULL;

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
        DROP TABLE `question_answer`;
        DROP TABLE `question_lang`;
        DROP TABLE `question`;
        DROP TABLE `questionnaire_answer`;
        DROP TABLE `questionnaire`;    ";
    }

}