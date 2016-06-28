-- Delete project passing first round today

DELETE FROM `invest` WHERE `project` = 'project-one-round-finishing';

DELETE IGNORE FROM `contract` WHERE `project` = 'project-one-round-finishing';

DELETE FROM `cost` WHERE `project` = 'project-one-round-finishing';

DELETE FROM `project` WHERE `id` = 'project-one-round-finishing';


DELETE FROM `project_conf` WHERE `project` = 'project-one-round-finishing';

DELETE FROM `user_pool` WHERE `user` IN (
    'owner-project-one-round-finishing',
    'backer-1-one-round-finishing',
    'backer-2-one-round-finishing',
    'backer-3-one-round-finishing',
    'backer-4-one-round-finishing');

DELETE FROM `user` WHERE `id` IN (
    'owner-project-one-round-finishing',
    'backer-1-one-round-finishing',
    'backer-2-one-round-finishing',
    'backer-3-one-round-finishing',
    'backer-4-one-round-finishing');


DELETE FROM event WHERE action LIKE 'project-one-round-finishing:%';
