CREATE TABLE IF NOT EXISTS invest_reward (
  invest bigint(20) unsigned NOT NULL,
  reward bigint(20) unsigned NOT NULL,
  fulfilled date DEFAULT NULL,
  UNIQUE KEY invest (invest,reward)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Recompensas elegidas al aportar';