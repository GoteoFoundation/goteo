CREATE TABLE  `post_tag` (
`post` BIGINT UNSIGNED NOT NULL ,
`tag` BIGINT UNSIGNED NOT NULL ,
PRIMARY KEY (  `post` ,  `tag` )
) ENGINE = INNODB COMMENT =  'Tags de las entradas';

-- constrains
DELETE FROM post_tag WHERE tag NOT IN (SELECT id FROM tag);
ALTER TABLE `post_tag` ADD FOREIGN KEY (`tag`) REFERENCES `tag`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
