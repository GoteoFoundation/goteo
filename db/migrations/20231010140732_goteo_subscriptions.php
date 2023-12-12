<?php

/**
 * Migration Task class.
 */
class GoteoSubscriptions
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
            ALTER TABLE `reward` ADD COLUMN `subscribable` int(1) DEFAULT 0;
            ALTER TABLE `project_account` ADD COLUMN `allow_stripe` int(1) DEFAULT 0 AFTER `allowpp`;
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
            ALTER TABLE `reward` DROP COLUMN `subscribable`;
            ALTER TABLE `project_account` DROP COLUMN `allow_stripe`;
        ";
    }
}
