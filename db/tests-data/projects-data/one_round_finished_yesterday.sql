-- Project one round finished yesterday

-- Owner

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('owner-project-one-round-finished', 'Owner project one round', 'owner-project-one-round-finished@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Owner project one round passing yesterday', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());


-- Backers

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-1-one-round-finished', 'Backer 1 project one round finished yesterday' , 'backer-1-one-round-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 1 project one round finished yesterday', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-2-one-round-finished', 'Backer 2 project one round finished yesterday', 'backer-2-one-round-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 2 project one round finished yesterday', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-3-one-round-finished', 'Backer 3 project one round finished yesterday', 'backer-3-one-round-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 3 project one round finished yesterday', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-4-one-round-finished', 'Backer 4 project one round finished yesterday', 'backer-4-one-round-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 4 project one round finished yesterday', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());



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
 'project-one-round-finished',
 'Project one round finished yesterday',
 'Description project one round finished yesterday',
 'es',
 'EUR',
 1.00000,
 4,
 1,
 110,
 'owner-project-one-round-finished',
 'goteo',
 220,
 200,
 400,
 1,
 2,
 0,
 0,
 0,
 NOW()-INTERVAL 47 DAY,
 NOW()-INTERVAL 41 DAY,
 NOW()-INTERVAL 41 DAY,
 NOW()-INTERVAL 1 DAY,
 NULL,
 NOW()-INTERVAL 1 DAY,
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

INSERT INTO `project_conf` (`project`, `noinvest`, `watch`, `days_round1`, `days_round2`, `one_round`, `help_license`, `help_cost`)
	values('project-one-round-finished','0','0','40','0','1','0','0');

-- costs
INSERT INTO `cost` (`project`, `cost`, `description`, `type`, `amount`, `required`, `from`, `until`) values('project-one-round-finished','Cost 1','Description cost 1','task','50','1','2016-05-03','2016-05-16');

INSERT INTO `cost` (`project`, `cost`, `description`, `type`, `amount`, `required`, `from`, `until`) values('project-one-round-finished','Cost 2','Description cost 2','task','150','1','2016-05-03','2016-05-16');

INSERT INTO `cost` (`project`, `cost`, `description`, `type`, `amount`, `required`, `from`, `until`) values('project-one-round-finished','Cost 3','Description cost 3','task','200','0','2016-05-03','2016-05-16');

-- Invests

INSERT INTO `invest` (`user`, `project`, `account`, `amount`, `amount_original`, `currency`, `currency_rate`, `status`, `anonymous`, `resign`, `invested`, `charged`, `returned`, `preapproval`, `payment`, `transaction`, `method`, `admin`, `campaign`, `datetime`, `drops`, `droped`, `call`, `issue`, `pool`) VALUES
('backer-1-one-round-finished', 'project-one-round-finished', '', 200, 200, 'EUR', 1.00000, 1, NULL, 1, NOW()-INTERVAL 37 DAY, NOW()-INTERVAL 37 DAY, NULL, NULL, '', NULL, 'dummy', NULL, NULL, NOW()-INTERVAL 62 DAY, NULL, NULL, NULL, NULL, 1),
('backer-2-one-round-finished', 'project-one-round-finished', '', 42, 42, 'EUR', 1.00000, 1, NULL, NULL, NOW()-INTERVAL 32 DAY, NOW()-INTERVAL 32 DAY, NULL, '', NULL, NULL, 'dummy', NULL, NULL, NOW()-INTERVAL 72 DAY, NULL, NULL, NULL, NULL, NULL);



