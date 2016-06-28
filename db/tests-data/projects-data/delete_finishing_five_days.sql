-- Delete project finishing today

DELETE FROM `invest_address` WHERE `invest` IN (SELECT id FROM invest where `project` = 'project-finishing-five-days');


DELETE FROM `invest` WHERE `project` = 'project-finishing-five-days';


DELETE FROM `project` WHERE `id` = 'project-finishing-five-days';

DELETE FROM `user_pool` WHERE `user` IN (
    'owner-project-finishing-five-days' ,
    'backer-1-finishing-five-days-project' ,
    'backer-2-finishing-five-days-project',
    'backer-3-finishing-five-days-project',
    'backer-4-finishing-five-days-project');

DELETE FROM `user` WHERE `id` IN (
    'owner-project-finishing-five-days' ,
    'backer-1-finishing-five-days-project' ,
    'backer-2-finishing-five-days-project',
    'backer-3-finishing-five-days-project',
    'backer-4-finishing-five-days-project');

DELETE FROM event WHERE action LIKE 'project-finishing-five-days:%';
