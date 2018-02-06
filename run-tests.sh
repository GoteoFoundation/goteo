#!/bin/bash

CURR=$(dirname $(readlink -f $0))

cd $CURR

ARGS=()
for var in "$@"; do
    if [ "$SET_CONFIG" == "1" ]; then
        CONFIG="$var"
        SET_CONFIG=
        continue
    fi
    if [ "$var" == '--help' ] || [ "$var" == '-h' ]; then
        echo "Wrap script for running tests"
        echo "Usage"
        echo -e "$0 [options|phpunit options]"
        echo "Options"
        echo -e "\t--reset-database (-r)\t Drops and reinstalls the database before testing"
        echo -e "\t--test-config (-t) file.yml\t Specifies a settings.yml file to be used for testing"
        echo "The rest of options will be passed to phpunit"
        exit
    # Remove --reset argument
    elif [ "$var" == '--reset-database' ] || [ "$var" == '-r' ]; then
        RESET_DATABASE=1
    elif [ "$var" == '--test-config' ] || [ "$var" == '-t' ]; then
        SET_CONFIG=1
        continue
    else
        ARGS+=("$var")
    fi
done

if [ ! -e "$CONFIG" ]; then
    echo "Custom [$CONFIG] file not found"
    CONFIG="config/test-settings.yml"
fi

if [ "${CONFIG:0:1}" != "/" ]; then
    CONFIG=$CURR/$CONFIG;
fi
if [ "${CONFIG:0:1}" != "." ]; then
    CONFIG=$(dirname $(readlink -f $CONFIG))/$(basename $CONFIG)
fi

echo "Using [$CONFIG] file for testing"
export GOTEO_TEST_CONFIG_FILE=$CONFIG;
export GOTEO_CONFIG_FILE=$CONFIG;

if [ "$RESET_DATABASE" != "" ]; then
    echo "Removing database"
    DB=$(cat $CONFIG | grep 'db:' -A8)
    #remove spaces
    DB=${DB// /}
    #find database
    DATABASE=$(echo "$DB" | grep 'database:')
    DATABASE=${DATABASE/database:/}
    DATABASE=${DATABASE%\#*}
    HOST=$(echo "$DB" | grep 'host:')
    HOST=${HOST/host:/}
    HOST=${HOST%\#*}
    PORT=$(echo "$DB" | grep 'port:')
    PORT=${PORT/port:/}
    PORT=${PORT%\#*}
    USER=$(echo "$DB" | grep 'username:')
    USER=${USER/username:/}
    USER=${USER%\#*}
    PASS=$(echo "$DB" | grep 'password:')
    PASS=${PASS/password:/}
    PASS=${PASS%\#*}
    echo $DATABASE $HOST:$PORT $USER $PASS

    if [[ $DATABASE != *"test"* ]]; then
        echo "[$DATABASE] should contain the work 'test' to reset schema from here"
        exit 2
    fi

    mysql -h $HOST -P $PORT -u$USER -p$PASS $DATABASE < $CURR/tests/sql_wipe.sql

    if [ "$?" != '0' ]; then
        echo "Error removing tables!"
        exit 2
    fi

    # check if password is empty
    if [ -z "$DATABASE" ]; then
        DATABASE=$HOST
        HOST=$PASS
        PASS=''
    else
        PASS="-p$PASS"
    fi

    GOTEO_CONFIG_FILE=$CONFIG ./bin/console migrate install
    if [ "$?" != '0' ]; then
        echo "Error installing tables & migrating schema!"
        exit 2
    fi

    echo "Done, now testing"
fi

./vendor/bin/phpunit "${ARGS[@]}"
