CREATE TABLE IF NOT EXISTS `user_lang` (
`id` varchar(50) COLLATE utf8_general_ci NOT NULL,
`lang` varchar(2) NOT NULL,
`about` text COLLATE utf8_general_ci DEFAULT NULL,
`keywords` tinytext COLLATE utf8_general_ci DEFAULT NULL,
`contribution` text COLLATE utf8_general_ci DEFAULT NULL,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

-- user name translate
ALTER TABLE `user_lang`
  ADD COLUMN `name` varchar(100) COLLATE utf8_general_ci DEFAULT NULL AFTER `about`;

  -- foreign keys user
DELETE FROM user_lang WHERE id NOT IN (SELECT id FROM USER);
ALTER TABLE `user_lang` ADD FOREIGN KEY (`id`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
