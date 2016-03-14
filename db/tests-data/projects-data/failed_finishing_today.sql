-- Project failed finishing today


-- Owner

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('owner-project-failed-finishing', 'Owner project failed-finishing', 'owner-project-failed-finishing@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Owner project failed-finishing', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

-- Backers

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-1-failed-finishing-project', 'Backer 1 failed-finishing project', 'backer-1-failed-finishing-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 1 failed-finishing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-2-failed-finishing-project', 'Backer 2 failed-finishing project', 'backer-2-failed-finishing-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 2 failed-finishing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-3-failed-finishing-project', 'Backer 3 failed-finishing project', 'backer-3-failed-finishing-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 3 failed-finishing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('backer-4-failed-finishing-project', 'Backer 4 failed-finishing project', 'backer-4-failed-finishing-project@goteo.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 4 failed-finishing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW());


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
 'project-failed-finishing-today',
 'Project failed-finishing today',
 'Project description failed-finishing today',
 'es',
 'EUR',
 1.00000,
 3,
 1,
 110,
 'owner-project-failed-finishing',
 'goteo',
 40,
 200,
 400,
 1,
 3,
 0,
 0,
 0,
 NOW()-INTERVAL 60 DAY,
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

-- Project accounts
INSERT INTO `project_account` (`project`, `bank`, `bank_owner`, `paypal`, `paypal_owner`, `allowpp`, `fee`) values('project-failed-finishing-today','','Testing user','testing@goteo.org','Testing user',NULL,'4');

INSERT INTO `cost` (`project`, `cost`, `description`, `type`, `amount`, `required`, `from`, `until`) values('project-failed-finishing-today','Cost 1','Description cost 1','task','50','1','2016-05-03','2016-05-16');

INSERT INTO `cost` (`project`, `cost`, `description`, `type`, `amount`, `required`, `from`, `until`) values('project-failed-finishing-today','Cost 2','Description cost 2','task','150','1','2016-05-03','2016-05-16');

INSERT INTO `cost` (`project`, `cost`, `description`, `type`, `amount`, `required`, `from`, `until`) values('project-failed-finishing-today','Cost 3','Description cost 3','task','200','0','2016-05-03','2016-05-16');

-- Invests

INSERT INTO `invest` (`user`,
 `project`,
 `account`, `amount`, `amount_original`, `currency`, `currency_rate`, `status`, `anonymous`, `resign`, `invested`, `charged`, `returned`, `preapproval`, `payment`, `transaction`, `method`, `admin`, `campaign`, `datetime`, `drops`, `droped`, `call`, `issue`, `pool`) VALUES
('backer-1-failed-finishing-project', 'project-failed-finishing-today', '', 20, 20, 'EUR', 1.00000, 1, NULL, 1, NOW()-INTERVAL 60 DAY, NOW()-INTERVAL 60 DAY, NULL, '727025821', '1200387012150822204948007100', '', 'dummy', NULL, NULL, NOW()-INTERVAL 60 DAY, NULL, NULL, NULL, NULL, NULL),
('backer-2-failed-finishing-project', 'project-failed-finishing-today', '', 50, 50, 'EUR', 1.00000, 1, NULL, NULL, NOW()-INTERVAL 65 DAY, NOW()-INTERVAL 65 DAY, NULL, '727001105', '1200386948150822192936007100', '', 'dummy', NULL, NULL, NOW()-INTERVAL 65 DAY, NULL, NULL, NULL, NULL, 1),
('backer-3-failed-finishing-project', 'project-failed-finishing-today', '', 10, 10, 'EUR', 1.00000, 1, NULL, NULL, NOW()-INTERVAL 70 DAY, NULL, NULL, 'PA-7X430535Y6705613F', NULL, NULL, 'dummy', NULL, NULL, NOW()-INTERVAL 70 DAY, NULL, NULL, NULL, NULL, NULL);



