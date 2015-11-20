-- Update key dates to expired

UPDATE `project` SET `published` =  NOW()-INTERVAL %DAYS_2_ROUND% DAY, `passed` =  NOW()-INTERVAL %DAYS_1_ROUND% DAY, `days`= 0 WHERE `id` = 'project-finishing-today';

UPDATE `project` SET `published` =  NOW()-INTERVAL %DAYS_1_ROUND% DAY, `days`=%DAYS_1_ROUND% WHERE `id` = 'project-passing-today';

UPDATE `project` SET `published` =  NOW()-INTERVAL %DAYS_1_ROUND% DAY, `success` =  NOW() - INTERVAL (%DAYS_1_ROUND% - 40) DAY, `days`=%DAYS_1_ROUND% WHERE `id` = 'project-one-round-finishing';

UPDATE `project` SET `published` =  NOW()-INTERVAL %DAYS_2_ROUND% DAY, `passed` =  NOW()-INTERVAL %DAYS_1_ROUND% DAY, `days`= 0 WHERE `id` = 'project-failed-finishing-today';
