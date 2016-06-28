-- Delete project finishing today

DELETE FROM `invest` WHERE `project` = 'project-finishing-today';

DELETE IGNORE FROM `contract` WHERE `project` = 'project-finishing-today';

DELETE FROM `cost` WHERE `project` = 'project-one-round-finishing';

DELETE FROM `project` WHERE `id` = 'project-finishing-today';

DELETE FROM `user_pool` WHERE `user` IN (
    'owner-project-finishing',
    'backer-1-finishing-project',
    'backer-2-finishing-project',
    'backer-3-finishing-project',
    'backer-4-finishing-project');

DELETE FROM `user` WHERE `id` IN (
    'owner-project-finishing',
    'backer-1-finishing-project',
    'backer-2-finishing-project',
    'backer-3-finishing-project',
    'backer-4-finishing-project');

DELETE FROM event WHERE action LIKE 'project-finishing-today:%';
