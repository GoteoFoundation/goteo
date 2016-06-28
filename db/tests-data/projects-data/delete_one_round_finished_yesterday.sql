-- Delete project finished first round yesterday

DELETE FROM `invest` WHERE `project` = 'project-one-round-finished';

DELETE IGNORE FROM `contract` WHERE `project` = 'project-one-round-finished';

DELETE FROM `cost` WHERE `project` = 'project-one-round-finished';

DELETE FROM `project` WHERE `id` = 'project-one-round-finished';


DELETE FROM `project_conf` WHERE `project` = 'project-one-round-finished';

DELETE FROM `user_pool` WHERE `user` IN (
    'owner-project-one-round-finished',
    'backer-1-one-round-finished',
    'backer-2-one-round-finished',
    'backer-3-one-round-finished',
    'backer-4-one-round-finished');

DELETE FROM `user` WHERE `id` IN (
    'owner-project-one-round-finished',
    'backer-1-one-round-finished',
    'backer-2-one-round-finished',
    'backer-3-one-round-finished',
    'backer-4-one-round-finished');


DELETE FROM event WHERE action LIKE 'project-one-round-finished:%';
