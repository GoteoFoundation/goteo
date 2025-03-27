<?php

/**
 * Migration Task class.
 */
class GoteoAnnouncement
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
        CREATE TABLE `announcement`  (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `title` TEXT NOT NULL,
            `description` TEXT NOT NULL,
            `type` VARCHAR(50) NOT NULL,
            `lang` VARCHAR(6) NOT NULL,
            `project_id` VARCHAR(50) COLLATE utf8_general_ci DEFAULT NULL,
            `cta_url` VARCHAR(255),
            `cta_text` VARCHAR(255),
            `active` INT(1) NOT NULL DEFAULT 0,
            `start_date` date NULL,
            `end_date` date NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp,
            `modified_at` timestamp NOT NULL DEFAULT current_timestamp on update CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        );

        CREATE TABLE `announcement_lang` (
            `id` BIGINT(20) UNSIGNED NOT NULL,
            `title` TEXT NOT NULL,
            `description` TEXT NOT NULL,
            `lang` VARCHAR(6) NOT NULL,
            `cta_url` VARCHAR(255),
            `cta_text` VARCHAR(255),
            FOREIGN KEY (`id`) REFERENCES `announcement` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        );
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
            DROP TABLE `announcement_lang`;
            DROP TABLE `announcement`;
     ";
    }
}
