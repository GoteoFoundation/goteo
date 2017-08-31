-- Fee to apply in the financial report to the drop
ALTER TABLE `call` ADD `fee_projects_drop` INT(2) NOT NULL DEFAULT 4 COMMENT 'Fee to apply in the financial report to the drop';

-- Adding order on gallery tables
ALTER TABLE `post_image` ADD COLUMN `order` TINYINT(4) DEFAULT 1 NOT NULL AFTER `image`;
ALTER TABLE `glossary_image` ADD COLUMN `order` TINYINT(4) DEFAULT 1 NOT NULL AFTER `image`;
ALTER TABLE `info_image` ADD COLUMN `order` TINYINT(4) DEFAULT 1 NOT NULL AFTER `image`;
