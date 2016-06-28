-- Delete project publishing today

DELETE FROM `cost` WHERE `project` = 'project-one-round-publishing';

DELETE FROM `project` WHERE `id` = 'project-publishing-today';

DELETE FROM `user` WHERE `id` IN (
    'owner-project-publishing');

DELETE FROM event WHERE action LIKE 'project-publishing-today:%';
