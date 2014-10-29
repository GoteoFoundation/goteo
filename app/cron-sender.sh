#!/bin/bash
cd "$(dirname "$0")"
LOG="logs/cli-sender-$(date +%Y-%m-%dT%H:%M)"
LOG_SEND="logs/cli-sendmail-"
TAR="logs/cli-sendmail-$(date +%Y-%m-%dT%H:%M)"
echo "procesando envios..."
echo "cron-sender:$(date)" > "$LOG.log"
/usr/bin/php $(dirname "$0")/cli-sender.php >> "$LOG.log" 2>&1

echo ">>>>>>> $LOG.log >>>>>>>"
cat "$LOG.log"
echo "<<<<<<< $LOG.log <<<<<<<"

#comprobar si el numero de lineas del log es menor a 2
LINES=$(cat "$LOG.log" | wc -l)
if [[ $LINES -gt 2 ]] ; then
	echo "comprimiendo y conservando log de envio"
	gzip "$LOG.log"
	echo "comprimiendo archivos generados"
	tar cfz "$TAR"".tar.gz" "$LOG_SEND"*.log
else
	echo "no se ha enviado nada, eliminamos el log"
	rm "$LOG.log"
fi

echo "Borrando logs de mas de 30 dias:"
echo "find logs/cli-send* -mtime +30 -exec rm {} \;"
find logs/cli-send* -mtime +30 -exec rm {} \;