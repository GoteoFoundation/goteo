
-- usuario root
INSERT INTO `user` (`id`, `name`, `location`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `google`, `identica`, `linkedin`, `worth`, `created`, `modified`, `token`, `hide`, `confirmed`, `lang`, `node`) VALUES('root', 'Super administrador', '', 'root_goteo@doukeshi.org', '', '', '', 1, 1, '', '', '', '', '', '', 0, '', '', '', 1, 1, NULL, NULL);


-- roles
INSERT INTO `user_role` (`user_id`, `role_id`, `node_id`, `datetime`) VALUES
('root', 'root', '*', NULL),
('root', 'superadmin', '*', NULL);
