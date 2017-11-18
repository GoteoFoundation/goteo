-- adding private messages
ALTER TABLE `message` ADD COLUMN `private` TINYINT(1) DEFAULT 0 NOT NULL AFTER `closed`;
CREATE TABLE `message_user`( `message_id` BIGINT UNSIGNED NOT NULL, `user_id` CHAR(50) NOT NULL, PRIMARY KEY (`message_id`, `user_id`), FOREIGN KEY (`message_id`) REFERENCES `message`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE );

-- Foreign indexs sanitization for support/message tables

UPDATE support SET thread=NULL WHERE thread NOT IN (SELECT id FROM message);
ALTER TABLE `support` ADD FOREIGN KEY (`thread`) REFERENCES `message`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
DELETE FROM support WHERE `project` NOT IN (SELECT id FROM `project`);
ALTER TABLE `support` ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

DELETE FROM support_lang WHERE id NOT IN (SELECT id FROM support);
ALTER TABLE `support_lang` CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL, ADD FOREIGN KEY (`id`) REFERENCES `support`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

DELETE FROM message_lang WHERE id NOT IN (SELECT id FROM message);
ALTER TABLE `message_lang` CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL, ADD FOREIGN KEY (`id`) REFERENCES `message`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

DELETE m.* FROM message m WHERE thread NOT IN (SELECT id FROM (SELECT id FROM message) n);
ALTER TABLE `message` ADD FOREIGN KEY (`thread`) REFERENCES `message`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

DELETE FROM message WHERE `user` NOT IN (SELECT id FROM `user`);
DELETE FROM message WHERE `project` NOT IN (SELECT id FROM `project`);
ALTER TABLE `message` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE `message` DROP INDEX `id`;

-- Invest optimizations
UPDATE invest SET anonymous=0 WHERE anonymous IS NULL;
UPDATE invest SET resign=0 WHERE resign IS NULL;
UPDATE invest SET campaign=0 WHERE campaign IS NULL;
UPDATE invest SET pool=0 WHERE pool IS NULL;
UPDATE invest SET issue=0 WHERE issue IS NULL;
ALTER TABLE `invest` CHANGE `anonymous` `anonymous` BOOLEAN DEFAULT 0 NOT NULL, CHANGE `resign` `resign` BOOLEAN DEFAULT 0 NOT NULL, CHANGE `campaign` `campaign` BOOLEAN DEFAULT 0 NOT NULL COMMENT 'si es un aporte de capital riego', CHANGE `issue` `issue` BOOLEAN DEFAULT 0 NOT NULL COMMENT 'Problemas con el cobro del aporte', CHANGE `pool` `pool` BOOLEAN DEFAULT 0 NOT NULL COMMENT 'A reservar si el proyecto falla';

-- foreign keys user
DELETE FROM user_lang WHERE id NOT IN (SELECT id FROM user);
ALTER TABLE `user_lang` ADD FOREIGN KEY (`id`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;


-- Post foreign keys
DELETE FROM post_lang WHERE id NOT IN (SELECT id FROM post);
ALTER TABLE `post_lang` ADD FOREIGN KEY (`id`) REFERENCES `post`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

DELETE FROM post WHERE blog NOT IN (SELECT id FROM blog);
ALTER TABLE `post` ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`blog`) REFERENCES `blog`(`id`);

UPDATE post_lang a JOIN post b ON a.id=b.id AND a.`blog` != b.blog SET a.blog = b.blog;
ALTER TABLE `post_lang` CHANGE `blog` `blog` BIGINT(20) UNSIGNED NOT NULL, ADD FOREIGN KEY (`blog`) REFERENCES `blog`(`id`);

ALTER TABLE `post_node` ADD FOREIGN KEY (`post`) REFERENCES `post`(`id`) ON UPDATE CASCADE;

DELETE FROM post_tag WHERE tag NOT IN (SELECT id FROM tag);
ALTER TABLE `post_tag` ADD FOREIGN KEY (`tag`) REFERENCES `tag`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;


-- costs constrains
DELETE FROM cost WHERE project NOT IN (SELECT id FROM project);
ALTER TABLE `cost` ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

DELETE FROM cost_lang WHERE id NOT IN (SELECT id FROM cost);
UPDATE cost_lang a JOIN cost b ON a.id=b.id AND a.project != b.project SET a.project = b.project;
ALTER TABLE `cost_lang` CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL, ADD FOREIGN KEY (`id`) REFERENCES `cost`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE;

-- rewards constrains
DELETE FROM reward WHERE project NOT IN (SELECT id FROM project);
ALTER TABLE `reward` ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE;

DELETE FROM reward_lang WHERE id NOT IN (SELECT id FROM reward);
UPDATE reward_lang a JOIN reward b ON a.id=b.id AND a.project != b.project SET a.project = b.project;
ALTER TABLE `reward_lang` CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL, ADD FOREIGN KEY (`id`) REFERENCES `reward`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE;


-- support lang
DELETE FROM support_lang WHERE id NOT IN (SELECT id FROM support);
UPDATE support_lang a JOIN support b ON a.id=b.id AND a.project != b.project SET a.project = b.project;
ALTER TABLE `support_lang` ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE;


-- project related missing foreign keys
ALTER TABLE `project_category` CHANGE `category` `category` INT(10) UNSIGNED NOT NULL, ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`category`) REFERENCES `category`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

DELETE FROM review WHERE project NOT IN (SELECT id FROM project);
ALTER TABLE `review` ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `project_lang` ADD FOREIGN KEY (`id`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;


-- delete project from call
ALTER TABLE `call_project` DROP FOREIGN KEY `call_project_ibfk_1`;
ALTER TABLE `call_project` ADD CONSTRAINT `call_project_ibfk_1` FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `call_project` DROP FOREIGN KEY `call_project_ibfk_2`;
ALTER TABLE `call_project` ADD CONSTRAINT `call_project_ibfk_2` FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;


-- contract spelling and constrains
ALTER TABLE `contract_status` CHANGE `recieved` `received` INT(1) DEFAULT 0 NOT NULL COMMENT 'Se ha recibido el contrato firmado', CHANGE `recieved_date` `received_date` DATE NULL COMMENT 'Fecha que se cambia el flag', CHANGE `recieved_user` `received_user` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NULL COMMENT 'Usuario que cambia el flag', ADD FOREIGN KEY (`owner_user`) REFERENCES `user`(`id`) ON UPDATE CASCADE;
ALTER TABLE `contract_status` ADD FOREIGN KEY (`admin_user`) REFERENCES `user`(`id`) ON UPDATE CASCADE;
ALTER TABLE `contract_status` ADD FOREIGN KEY (`pdf_user`) REFERENCES `user`(`id`) ON UPDATE CASCADE;
ALTER TABLE `contract_status` ADD FOREIGN KEY (`payed_user`) REFERENCES `user`(`id`) ON UPDATE CASCADE;
ALTER TABLE `contract_status` ADD FOREIGN KEY (`prepay_user`) REFERENCES `user`(`id`) ON UPDATE CASCADE, ADD FOREIGN KEY (`closed_user`) REFERENCES `user`(`id`) ON UPDATE CASCADE;
ALTER TABLE `contract_status` ADD FOREIGN KEY (`ready_user`) REFERENCES `user`(`id`) ON UPDATE CASCADE;
ALTER TABLE `contract_status` ADD FOREIGN KEY (`received_user`) REFERENCES `user`(`id`) ON UPDATE CASCADE;

-- mime type for documents
ALTER TABLE `document` CHANGE `type` `type` VARCHAR(120) CHARSET utf8 COLLATE utf8_general_ci NULL;

-- add facebook_pixel for calls
ALTER TABLE `call` ADD COLUMN `facebook_pixel` varchar(20)  COLLATE utf8_general_ci NULL after `fee_projects_drop`;

-- user interests constrains
DELETE FROM user_interest WHERE `user` NOT IN (SELECT id FROM `user`);
ALTER TABLE `user_interest` CHANGE `interest` `interest` INT(10) UNSIGNED NOT NULL, ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`interest`) REFERENCES `category`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

-- Sphere order
ALTER TABLE `call_sphere` ADD `order` SMALLINT UNSIGNED NOT NULL DEFAULT '1' AFTER `sphere`;

-- call constrains
ALTER TABLE `call` ADD FOREIGN KEY (`owner`) REFERENCES `user`(`id`) ON UPDATE CASCADE;
ALTER TABLE `call_banner` ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `call_banner` CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `call_banner_lang` CHANGE `id` `id` INT(11) UNSIGNED NOT NULL, ADD PRIMARY KEY (`id`, `lang`);
DELETE FROM call_banner_lang WHERE id NOT IN (SELECT id FROM call_banner);
ALTER TABLE `call_banner_lang` ADD FOREIGN KEY (`id`) REFERENCES `call_banner`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `call_category` ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `call_category` CHANGE `category` `category` INT(10) UNSIGNED NOT NULL, ADD FOREIGN KEY (`category`) REFERENCES `category`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `call_conf` ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
DELETE FROM call_icon WHERE icon NOT IN (SELECT id FROM icon);
ALTER TABLE `call_icon` ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`icon`) REFERENCES `icon`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
DELETE FROM call_lang WHERE id NOT IN (SELECT id FROM `call`);
ALTER TABLE `call_lang` ADD FOREIGN KEY (`id`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `call_post` CHANGE `post` `post` BIGINT(20) UNSIGNED NOT NULL, ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`post`) REFERENCES `post`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `call_sphere` CHANGE `sphere` `sphere` BIGINT(20) UNSIGNED NOT NULL, ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`sphere`) REFERENCES `sphere`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `call_sponsor` ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `campaign` ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE `category_lang` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL, ADD FOREIGN KEY (`id`) REFERENCES `category`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

-- project milestones constrains
DELETE FROM project_milestone WHERE post NOT IN (SELECT id FROM post);
DELETE FROM project_milestone WHERE project NOT IN (SELECT id FROM project);
ALTER TABLE `project_milestone` CHANGE `milestone` `milestone` BIGINT(20) UNSIGNED NULL, CHANGE `post` `post` BIGINT(20) UNSIGNED NULL, ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`milestone`) REFERENCES `milestone`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`post`) REFERENCES `post`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

-- matcher tables
CREATE TABLE `matcher` (
  `id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `lang` varchar(2) NOT NULL,
  `owner` varchar(50) NOT NULL,
  `terms` longtext NULL,
  `processor` varchar(50) NOT NULL DEFAULT '' COMMENT 'ID for the MatcherProcessor that handles the logic of this matcher',
  `vars` text NOT NULL COMMENT 'Customizable vars to be used in the processor',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `used` int(10) unsigned NOT NULL DEFAULT '0',
  `crowd` int(10) unsigned NOT NULL DEFAULT '0',
  `projects` int(10) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` date DEFAULT NULL,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`),
  CONSTRAINT `matcher_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `matcher_project` (
  `matcher_id` varchar(50) NOT NULL,
  `project_id` varchar(50) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'pending' COMMENT 'pending, accepted, active (funding ok), rejected (discarded by user), discarded (by admin)',
  PRIMARY KEY (`matcher_id`,`project_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `matcher_project_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `matcher_project_ibfk_2` FOREIGN KEY (`matcher_id`) REFERENCES `matcher` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `matcher_user` (
  `matcher_id` varchar(50) NOT NULL COMMENT 'Matcher campaign',
  `user_id` varchar(50) NOT NULL COMMENT 'User owner',
  `pool` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Use owner''s pool as funding source',
  PRIMARY KEY (`matcher_id`,`user_id`),
  KEY `matcher_user_ibfk_1` (`user_id`),
  CONSTRAINT `matcher_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `matcher_user_ibfk_2` FOREIGN KEY (`matcher_id`) REFERENCES `matcher` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

-- invests matcher
ALTER TABLE `invest` ADD COLUMN `matcher` VARCHAR(50) NULL AFTER `call`,
    ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE,
    ADD FOREIGN KEY (`matcher`) REFERENCES `matcher`(`id`) ON UPDATE CASCADE;


-- remove template constrains from mail as it can come from yaml sources now
ALTER TABLE `mail` CHANGE `template` `template` VARCHAR(100) NULL, DROP FOREIGN KEY `mail_ibfk_3`;


/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

/* Alter table in target */
ALTER TABLE `call_sponsor`
    ADD COLUMN `main` tinyint(1)   NOT NULL DEFAULT 1 COMMENT 'Sponsor main' after `amount` ;

/* Alter table in target */
ALTER TABLE `node`
    ADD COLUMN `owner_font_color` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Color de fuente módulo owner' after `home_img` ,
    ADD COLUMN `owner_social_color` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Color de iconos sociales módulo owner' after `owner_font_color` ;

/* Create table in target */
CREATE TABLE `origin`(
    `id` int(10) unsigned NOT NULL  auto_increment ,
    `tag` char(50) COLLATE utf8_general_ci NOT NULL  ,
    `category` char(50) COLLATE utf8_general_ci NOT NULL  ,
    `type` enum('referer','ua') COLLATE utf8_general_ci NOT NULL  COMMENT 'referer, ua' ,
    `project_id` char(50) COLLATE utf8_general_ci NULL  ,
    `invest_id` bigint(20) unsigned NULL  ,
    `call_id` char(50) COLLATE utf8_general_ci NULL  ,
    `counter` int(10) unsigned NOT NULL  DEFAULT 0 ,
    `created_at` datetime NULL  ,
    `modified_at` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `project`(`tag`,`project_id`,`type`,`category`) ,
    KEY `project_id`(`project_id`) ,
    KEY `invest_id`(`invest_id`) ,
    KEY `call_id`(`call_id`) ,
    KEY `call`(`tag`,`category`,`type`,`call_id`) ,
    KEY `invest`(`tag`,`category`,`type`,`invest_id`) ,
    CONSTRAINT `origin_ibfk_1`
    FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    CONSTRAINT `origin_ibfk_2`
    FOREIGN KEY (`invest_id`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    CONSTRAINT `origin_ibfk_3`
    FOREIGN KEY (`call_id`) REFERENCES `call` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Alter table in target */
ALTER TABLE `post_tag`
    ADD KEY `post_tag_ibfk_2`(`tag`) ,
    DROP KEY `tag` ,
    DROP FOREIGN KEY `post_tag_ibfk_1`  ;
ALTER TABLE `post_tag`
    ADD CONSTRAINT `post_tag_ibfk_1`
    FOREIGN KEY (`post`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    ADD CONSTRAINT `post_tag_ibfk_2`
    FOREIGN KEY (`tag`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `project_account`
    ADD COLUMN `skip_login` int(1)   NOT NULL DEFAULT 0 after `vat` ;

DROP TABLE `lang`;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
