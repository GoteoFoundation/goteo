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

-- adding private messages
ALTER TABLE `message` ADD COLUMN `private` TINYINT(1) DEFAULT 0 NOT NULL AFTER `closed`;
CREATE TABLE `message_user`( `message_id` BIGINT UNSIGNED NOT NULL, `user_id` CHAR(50) NOT NULL, PRIMARY KEY (`message_id`, `user_id`), FOREIGN KEY (`message_id`) REFERENCES `message`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE );

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
