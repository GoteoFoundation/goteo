-- Project publishing today

-- Owner

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('owner-project-publishing', 'Owner project publishing', 'owner-project-publishing@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Owner project publishing', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

-- Project
INSERT INTO `project` (`id`,
  `name`,
  `subtitle`,
  `lang`,
  `currency`,
  `currency_rate`,
  `status`,
  `translate`,
  `progress`,
  `owner`,
  `node`,
  `amount`,
  `mincost`,
  `maxcost`,
  `days`,
  `num_investors`,
  `popularity`,
  `num_messengers`,
  `num_posts`,
  `created`,
  `updated`,
  `published`,
  `success`,
  `closed`,
  `passed`,
  `contract_name`,
  `contract_nif`,
  `phone`,
  `contract_email`,
  `address`,
  `zipcode`,
  `location`,
  `country`,
  `image`,
  `description`,
  `video`,
  `project_location`
  ) VALUES (
 'project-publishing-today',
 'Project publishing today',
 'Description Project publishing today',
 'es',
 'EUR',
 1.00000,
 2,
 1,
 110,
 'owner-project-publishing',
 'goteo',
 440,
 200,
 400,
 1,
 3,
 0,
 0,
 0,
 NOW()-INTERVAL 10 DAY,
 NOW(),
 NOW(),
 NULL,
 NULL,
 NULL,
'User testing',
 '00000000-N',
 '00340000000000',
 'tester@goteo.org',
 'Dir tester',
 '00000',
 'Barcelona',
 'España',
 '7_10.jpg',
 'Testing project diseño participativo y auto-construcción',
 'https://vimeo.com/81621213',
 'City, country');


-- costs
INSERT INTO `cost` (`project`, `cost`, `description`, `type`, `amount`, `required`, `from`, `until`) values
('project-publishing-today','Cost 1','Description cost 1','task','50','1','2016-05-03','2016-05-16');

INSERT INTO `cost` (`project`, `cost`, `description`, `type`, `amount`, `required`, `from`, `until`) values
('project-publishing-today','Cost 2','Description cost 2','task','150','1','2016-05-03','2016-05-16');

INSERT INTO `cost` (`project`, `cost`, `description`, `type`, `amount`, `required`, `from`, `until`) values
('project-publishing-today','Cost 3','Description cost 3','task','200','0','2016-05-03','2016-05-16');




