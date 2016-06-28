-- Delete project passing first round today

DELETE FROM `invest` WHERE `project` = 'project-passing-today';

DELETE FROM `contract` WHERE `project` = 'project-passing-today';

DELETE FROM `project` WHERE `id` = 'project-passing-today';

DELETE FROM `user_pool` WHERE `user` IN (
    'owner-project-passing',
    'backer-1-passing-project',
    'backer-2-passing-project',
    'backer-3-passing-project',
    'backer-4-passing-project');

DELETE FROM `user` WHERE `id` IN (
    'owner-project-passing',
    'backer-1-passing-project',
    'backer-2-passing-project',
    'backer-3-passing-project',
    'backer-4-passing-project');


DELETE FROM event WHERE action LIKE 'project-passing-today:%';
