-- Project passing first today


-- Owner

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('owner-project-passing', 'Owner project passing', 'owner-project-passing@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Owner project passing', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());


-- Backers

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-1-passing-project', 'Backer 1 passing project', 'backer-1-passing-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 1 passing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-2-passing-project', 'Backer 2 passing project', 'backer-2-passing-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 2 passing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-3-passing-project', 'Backer 3 passing project', 'backer-3-passing-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 3 passing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-4-passing-project', 'Backer 4 passing project', 'backer-4-passing-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 4 passing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());



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
 'project-passing-today',
 'Project passing today',
 'Description Project passing today',
 'es',
 'EUR',
 1.00000,
 3,
 1,
 110,
 'owner-project-passing',
 'goteo',
 220,
 200,
 400,
 41,
 2,
 0,
 0,
 0,
 NOW()-INTERVAL 45 DAY,
 NOW()-INTERVAL 39 DAY,
 NOW()-INTERVAL 39 DAY,
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


-- Invests

INSERT INTO `invest` (`user`, `project`, `account`, `amount`, `amount_original`, `currency`, `currency_rate`, `status`, `anonymous`, `resign`, `invested`, `charged`, `returned`, `preapproval`, `payment`, `transaction`, `method`, `admin`, `campaign`, `datetime`, `drops`, `droped`, `call`, `issue`, `pool`) VALUES
('backer-1-passing-project', 'project-passing-today', '', 200, 200, 'EUR', 1.00000, 1, NULL, 1, NOW()-INTERVAL 35 DAY, NOW()-INTERVAL 35 DAY, NULL, NULL, '', NULL, 'dummy', NULL, NULL, NOW()-INTERVAL 60 DAY, NULL, NULL, NULL, NULL, 1),
('backer-2-passing-project', 'project-passing-today', '', 40, 40, 'EUR', 1.00000, 1, NULL, NULL, NOW()-INTERVAL 30 DAY, NOW()-INTERVAL 30 DAY, NULL, '', NULL, NULL, 'dummy', NULL, NULL, NOW()-INTERVAL 70 DAY, NULL, NULL, NULL, NULL, NULL);



