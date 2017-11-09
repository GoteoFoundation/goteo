CREATE TABLE `matcher` (
  `id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `lang` varchar(2) NOT NULL,
  `terms` longtext NULL,
  `processor` varchar(50) NOT NULL DEFAULT '' COMMENT 'ID for the MatcherProcessor that handles the logic of this matcher',
  `vars` text NOT NULL COMMENT 'Customizable vars to be used in the processor',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `used` int(10) unsigned NOT NULL DEFAULT '0',
  `crowd` int(10) unsigned NOT NULL DEFAULT '0',
  `projects` int(10) unsigned NOT NULL DEFAULT '0',
  `created` date DEFAULT NULL,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

