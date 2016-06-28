-- Delete project passed first round yesterday

DELETE FROM `invest` WHERE `project` = 'project-passed-yesterday';

DELETE FROM `contract` WHERE `project` = 'project-passed-yesterday';

DELETE FROM `project` WHERE `id` = 'project-passed-yesterday';

DELETE FROM `user_pool` WHERE `user` IN (
    'owner-project-passed',
    'backer-1-passed-project',
    'backer-2-passed-project',
    'backer-3-passed-project',
    'backer-4-passed-project');

DELETE FROM `user` WHERE `id` IN (
    'owner-project-passed',
    'backer-1-passed-project',
    'backer-2-passed-project',
    'backer-3-passed-project',
    'backer-4-passed-project');


DELETE FROM event WHERE action LIKE 'project-passed-yesterday:%';
