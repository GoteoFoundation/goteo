#!/bin/bash
cd "$(dirname "$0")"
/usr/bin/php cli-sender.php > logs/cli-sender.log 2>&1

