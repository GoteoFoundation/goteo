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
DELETE FROM user_lang WHERE id NOT IN (SELECT id FROM USER);
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
ALTER TABLE `cost` ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`);

DELETE FROM cost_lang WHERE id NOT IN (SELECT id FROM cost);
UPDATE cost_lang a JOIN cost b ON a.id=b.id AND a.project != b.project SET a.project = b.project;
ALTER TABLE `cost_lang` CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL, ADD FOREIGN KEY (`id`) REFERENCES `cost`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE;
