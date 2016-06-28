-- Delete project failed finishing today

DELETE FROM `invest_address` WHERE `invest` IN (SELECT id FROM invest where `project` = 'project-failed-finishing-today');

DELETE FROM `invest` WHERE `project` = 'project-failed-finishing-today';

DELETE FROM `project_account` WHERE `project` = 'project-failed-finishing-today';

DELETE FROM `cost` WHERE `project` = 'project-failed-finishing-today';

DELETE FROM `project` WHERE `id` = 'project-failed-finishing-today';


DELETE FROM `user_pool` WHERE `user` IN (
    'owner-project-failed-finishing' ,
    'backer-1-failed-finishing-project',
    'backer-2-failed-finishing-project',
    'backer-3-failed-finishing-project',
    'backer-4-failed-finishing-project');

DELETE FROM `user` WHERE `id` IN (
    'owner-project-failed-finishing' ,
    'backer-1-failed-finishing-project',
    'backer-2-failed-finishing-project',
    'backer-3-failed-finishing-project',
    'backer-4-failed-finishing-project');

DELETE FROM event WHERE action LIKE 'project-failed-finishing-today:%';
