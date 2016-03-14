-- Project finishing five days


-- Owner

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('owner-project-finishing-five-days', 'Owner project finishing', 'owner-project-finishing-five-days@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Owner project finishing', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

-- Backers

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-1-finishing-five-days-project', 'Backer 1 finishing five days project', 'backer-1-finishing-five-days-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 1 finishing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-2-finishing-five-days-project', 'Backer 2 five days finishing project', 'backer-2-finishing-five-days-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 2 finishing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-3-finishing-five-days-project', 'Backer 3 five days finishing project', 'backer-3-finishing-five-days-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 3 finishing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-4-finishing-five-days-project', 'Backer 4 five days finishing project', 'backer-4-finishing-five-days-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 4 finishing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());


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
 'project-finishing-five-days',
 'Project finishing five-days',
 'Description Project finishing five-days',
 'es',
 'EUR',
 1.00000,
 3,
 1,
 110,
 'owner-project-finishing-five-days',
 'goteo',
 440,
 200,
 400,
 5,
 3,
 0,
 0,
 0,
 NOW()-INTERVAL 100 DAY,
 NOW()-INTERVAL 75 DAY,
 NOW()-INTERVAL 75 DAY,
 NULL,
 NULL,
 NOW()-INTERVAL 35 DAY,
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
('backer-1-finishing-five-days-project', 'project-finishing-five-days', '', 200, 200, 'EUR', 1.00000, 1, NULL, 1, NOW()-INTERVAL 60 DAY, NOW()-INTERVAL 60 DAY, NULL, '727025821', '1200387012150822204948007100', '', 'dummy', NULL, NULL, NOW()-INTERVAL 60 DAY, NULL, NULL, NULL, NULL, NULL),
('backer-2-finishing-five-days-project', 'project-finishing-five-days', '', 200, 200, 'EUR', 1.00000, 1, NULL, NULL, NOW()-INTERVAL 65 DAY, NOW()-INTERVAL 65 DAY, NULL, '727001105', '1200386948150822192936007100', '', 'dummy', NULL, NULL, NOW()-INTERVAL 65 DAY, NULL, NULL, NULL, NULL, 1),
('backer-3-finishing-five-days-project', 'project-finishing-five-days', '', 40, 40, 'EUR', 1.00000, 1, NULL, NULL, NOW()-INTERVAL 70 DAY, NULL, NULL, 'PA-7X430535Y6705613F', NULL, NULL, 'dummy', NULL, NULL, NOW()-INTERVAL 70 DAY, NULL, NULL, NULL, NULL, NULL);



