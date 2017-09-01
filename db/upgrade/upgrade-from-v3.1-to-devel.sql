-- Foreign indexs sanitization for support/message tables

UPDATE support SET thread=NULL WHERE thread NOT IN (SELECT id FROM message);
ALTER TABLE `support` ADD FOREIGN KEY (`thread`) REFERENCES `message`(`id`) ON UPDATE SET NULL ON DELETE SET NULL;
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
