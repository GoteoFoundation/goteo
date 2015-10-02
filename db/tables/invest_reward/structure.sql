CREATE TABLE IF NOT EXISTS invest_reward (
  invest bigint(20) unsigned NOT NULL,
  reward bigint(20) unsigned NOT NULL,
  fulfilled tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY invest (invest,reward)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Recompensas elegidas al aportar';

-- Alter table por si no se puede pasar el create
ALTER TABLE `invest_reward` ADD `fulfilled` BOOLEAN NOT NULL DEFAULT '0';

ALTER TABLE `invest_reward` ADD INDEX `reward` ( `reward` ) ;