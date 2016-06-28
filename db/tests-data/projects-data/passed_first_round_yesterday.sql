-- Project passed first yesterday


-- Owner

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('owner-project-passed', 'Owner project passing', 'owner-project-passed@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Owner project passing', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());


-- Backers

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-1-passed-project', 'Backer 1 passing project', 'backer-1-passed-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 1 passing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-2-passed-project', 'Backer 2 passing project', 'backer-2-passed-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 2 passing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-3-passed-project', 'Backer 3 passing project', 'backer-3-passed-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 3 passing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-4-passed-project', 'Backer 4 passing project', 'backer-4-passed-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 4 passing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());



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
 'project-passed-yesterday',
 'Project passed yesterday',
 'Description Project passed yesterday',
 'es',
 'EUR',
 1.00000,
 3,
 1,
 110,
 'owner-project-passed',
 'goteo',
 220,
 200,
 400,
 42,
 2,
 0,
 0,
 0,
 NOW()-INTERVAL 46 DAY,
 NOW()-INTERVAL 40 DAY,
 NOW()-INTERVAL 40 DAY,
 NULL,
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


-- Invests

INSERT INTO `invest` (`user`, `project`, `account`, `amount`, `amount_original`, `currency`, `currency_rate`, `status`, `anonymous`, `resign`, `invested`, `charged`, `returned`, `preapproval`, `payment`, `transaction`, `method`, `admin`, `campaign`, `datetime`, `drops`, `droped`, `call`, `issue`, `pool`) VALUES
('backer-1-passed-project', 'project-passed-yesterday', '', 200, 200, 'EUR', 1.00000, 1, NULL, 1, NOW()-INTERVAL 36 DAY, NOW()-INTERVAL 36 DAY, NULL, NULL, '', NULL, 'dummy', NULL, NULL, NOW()-INTERVAL 60 DAY, NULL, NULL, NULL, NULL, 1),
('backer-2-passed-project', 'project-passed-yesterday', '', 40, 40, 'EUR', 1.00000, 1, NULL, NULL, NOW()-INTERVAL 31 DAY, NOW()-INTERVAL 31 DAY, NULL, '', NULL, NULL, 'dummy', NULL, NULL, NOW()-INTERVAL 70 DAY, NULL, NULL, NULL, NULL, NULL);



