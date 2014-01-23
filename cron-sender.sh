#!/bin/bash
cd "$(dirname "$0")"
echo "comprimiendo archivos antiguos..."
tar cfz "logs/sender-$(date +%Y-%m-%dT%H:%I).tar.gz" logs/cli-send*.log
echo "procesando envios..."
rm logs/cli-send*.log
echo "cron-sender:$(date)" > logs/cli-sender.log
/usr/bin/php cli-sender.php >> logs/cli-sender.log 2>&1
