#!/bin/bash

CURR=$(dirname $(readlink -f $0))
LOGDIR="$(dirname $CURR)/var/logs/mailing"
mkdir -p $LOGDIR
LOG="$LOGDIR/cli-sender-$(date +%Y-%m-%dT%H:%M)"
ret=0
echo "procesando envios..."
echo "cron-sender:$(date)" > "$LOG.log"

/usr/bin/php $CURR/cli-sender.php >> "$LOG.log" 2>&1

echo ">>>>>>> $LOG.log >>>>>>>"
cat "$LOG.log"
echo "<<<<<<< $LOG.log <<<<<<<"

#comprobar si el numero de lineas del log es menor a 2
LINES=$(cat "$LOG.log" | wc -l)
if [[ $LINES -gt 2 ]] ; then
	echo "comprimiendo y conservando log de envio"
	gzip "$LOG.log"
else
	echo "no se ha enviado nada, eliminamos el log"
	rm "$LOG.log"
fi

echo "Borrando logs de mas de 60 dias:"
echo "find $LOGDIR/cli-send* -mtime +60 -delete"
find "$LOGDIR/cli-send*" -mtime +60 -delete 2>/dev/null

#exit good, even if no files found
exit $ret
