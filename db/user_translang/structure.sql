CREATE TABLE `user_translang` (
`user` VARCHAR( 50 ) NOT NULL ,
`lang` VARCHAR( 2 ) NOT NULL ,
PRIMARY KEY ( `user` , `lang` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Idiomas de traductores';

-- Para los traductores existentes
REPLACE INTO user_translang (user, lang)
SELECT 
	user_role.user_id as user,
	lang.id as lang
FROM lang, user_role
WHERE user_role.role_id LIKE 'translator'
AND lang.id != 'es'

