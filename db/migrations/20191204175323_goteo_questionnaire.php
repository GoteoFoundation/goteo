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
                `lang` varchar(3) NOT NULL,
                PRIMARY KEY (`id`)
            );

        CREATE TABLE `questionnaire_matcher` (
            `questionnaire` BIGINT(20) UNSIGNED NOT NULL,
            `matcher` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
            PRIMARY KEY (`questionnaire`),
            CONSTRAINT `questionnaire_matcher_ibfk` FOREIGN KEY (`questionnaire`) REFERENCES `questionnaire` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `questionnaire_matcher_ibfk_2` FOREIGN KEY (`matcher`) REFERENCES `matcher` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        );

        CREATE TABLE `question` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `questionnaire` BIGINT(20) UNSIGNED NOT NULL,
            `lang` varchar(3) NOT NULL,
            `title` text,
            `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
            `max_score` INT(2) NOT NULL DEFAULT 0,
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

        CREATE TABLE `question_answer` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `question` BIGINT(20) UNSIGNED NOT NULL,
            `answer` TEXT,
            PRIMARY KEY (`id`),
            CONSTRAINT `question_answer_question` FOREIGN KEY (`question`) REFERENCES `question` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
        );

        CREATE TABLE `question_answer_project` (
            `answer` BIGINT(20) UNSIGNED NOT NULL,
            `project` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
            CONSTRAINT `question_answer_ibfk` FOREIGN KEY (`answer`) REFERENCES `question_answer` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            CONSTRAINT `question_answer_project_ibfk` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
        );

        CREATE TABLE `question_score` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `question` BIGINT(20) UNSIGNED NOT NULL,
            `answer` BIGINT(20) UNSIGNED NOT NULL,
            `score` INT(3) NOT NULL,
            `evaluator` VARCHAR(50) COLLATE utf8_general_ci NOT NULL,
            PRIMARY KEY (`id`),
            CONSTRAINT `question_score_question` FOREIGN KEY (`question`) REFERENCES `question`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            CONSTRAINT `question_score_evaluator` FOREIGN KEY (`evaluator`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `question_score_answer` FOREIGN KEY (`answer`) REFERENCES `question_answer` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
        );

        ALTER TABLE document MODIFY COLUMN contract varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;

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
        DROP TABLE `question_score`;
        DROP TABLE `question_answer_project`;
        DROP TABLE `question_answer`;
        DROP TABLE `question_lang`;
        DROP TABLE `question`;
        DROP TABLE `questionnaire_matcher`;
        DROP TABLE `questionnaire`;
        ";
    }

}