#!/bin/bash
cd "$(dirname "$0")"
echo "cron-sender: "`date` > logs/cli-sender.log
/usr/bin/php cli-sender.php >> logs/cli-sender.log 2>&1

