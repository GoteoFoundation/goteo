<?php

/**
 * Migration Task class.
 */
class GoteoFormHoneypot
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
            CREATE TABLE `form_honeypot` (
                `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `trap` VARCHAR(50) NOT NULL,
                `prey` TEXT,
                `template` TEXT,
                `datetime` TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ";
    }

    /**
     * Return the SQL statements for the Down migration
     *
     * @return string The SQL string to execute for the Down migration.
     */
    public function getDownSQL()
    {
        return "DROP TABLE `form_honeypot`";
    }
}
