/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

/* Foreign Keys must be dropped in the target to ensure that requires changes can be done*/

ALTER TABLE `invest_node`
    DROP FOREIGN KEY `invest_node_ibfk_1`  ,
    DROP FOREIGN KEY `invest_node_ibfk_2`  ,
    DROP FOREIGN KEY `invest_node_ibfk_3`  ;



/* Alter table in target */
ALTER TABLE `invest`
    ADD CONSTRAINT `invest_ibfk_1`
    FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON UPDATE CASCADE ,
    ADD CONSTRAINT `invest_ibfk_2`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `invest_address`
    ADD KEY `user`(`user`) ;

DELETE FROM invest_address WHERE invest NOT IN (SELECT id FROM invest);

ALTER TABLE `invest_address`
    ADD CONSTRAINT `invest_address_ibfk_1`
    FOREIGN KEY (`invest`) REFERENCES `invest` (`id`) ON UPDATE CASCADE ,
    ADD CONSTRAINT `invest_address_ibfk_2`
    FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `invest_detail`
    CHANGE `invest` `invest` bigint(20) unsigned   NOT NULL first ;

DELETE FROM invest_detail WHERE invest NOT IN (SELECT id FROM invest);

ALTER TABLE `invest_detail`
    ADD CONSTRAINT `invest_detail_ibfk_1`
    FOREIGN KEY (`invest`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `invest_node`
    CHANGE `invest_id` `invest_id` bigint(20) unsigned   NOT NULL after `project_node` ;
ALTER TABLE `invest_node`
    ADD CONSTRAINT `invest_node_ibfk_4`
    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    ADD CONSTRAINT `invest_node_ibfk_5`
    FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    ADD CONSTRAINT `invest_node_ibfk_6`
    FOREIGN KEY (`invest_id`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Alter table in target */

DELETE FROM invest_reward WHERE invest NOT IN (SELECT id FROM invest);
DELETE FROM invest_reward WHERE reward NOT IN (SELECT id FROM reward);

ALTER TABLE `invest_reward`
    ADD CONSTRAINT `invest_reward_ibfk_1`
    FOREIGN KEY (`invest`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    ADD CONSTRAINT `invest_reward_ibfk_2`
    FOREIGN KEY (`reward`) REFERENCES `reward` (`id`) ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `patron`
    ADD KEY `project`(`project`) ;
ALTER TABLE `patron`
    ADD CONSTRAINT `patron_ibfk_2`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* The foreign keys that were dropped are now re-created*/

ALTER TABLE `invest_node`
    ADD CONSTRAINT `invest_node_ibfk_1`
    FOREIGN KEY (`user_node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ,
    ADD CONSTRAINT `invest_node_ibfk_2`
    FOREIGN KEY (`project_node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ,
    ADD CONSTRAINT `invest_node_ibfk_3`
    FOREIGN KEY (`invest_node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
