-- Update key dates to expired

UPDATE `project` SET
    `published` =  NOW() - INTERVAL %DELTA% DAY,
    `days`= %DELTA%,
    `status` = %STATUS_ACTIVE%
WHERE `id` = 'project-publishing-today';

UPDATE `project` SET
    `published` =  NOW() - INTERVAL (79 + %DELTA%) DAY,
    `passed` =  NOW() - INTERVAL (39 + %DELTA%) DAY,
    `success` =  NOW() - INTERVAL %DELTA% DAY,
    `days`= 80,
    `status` = %STATUS_SUCCESS%
WHERE `id` = 'project-finishing-today';

UPDATE `project` SET
    `published` =  NOW() - INTERVAL (79 + %DELTA%) DAY,
    `passed` =  NOW() - INTERVAL %DELTA% DAY,
    `days` = (79 + %DELTA%),
    `status` = %STATUS_SUCCESS%
WHERE `id` = 'project-passing-today';

UPDATE `project` SET
    `published` =  NOW() - INTERVAL (79 + %DELTA%) DAY,
    `passed` =  NOW() - INTERVAL %DELTA% DAY,
    `days` = (79 + %DELTA%),
    `status` = %STATUS_SUCCESS%
WHERE `id` = 'project-passed-yesterday';

UPDATE `project` SET
    `published` =  NOW() - INTERVAL (79 + %DELTA%) DAY,
    `closed` =  NOW() - INTERVAL %DELTA% DAY,
    `days` = 80,
    `status` = %STATUS_FAILED%
WHERE `id` = 'project-failed-finishing-today';

UPDATE `project` SET
    `published` =  NOW() - INTERVAL (39 + %DELTA%) DAY,
    `success` =  NOW() - INTERVAL (%DELTA% - 1) DAY,
    `days` = 40,
    `status` = %STATUS_SUCCESS%
WHERE `id` = 'project-one-round-finishing';
