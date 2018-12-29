#!/bin/bash

# Add local user
# Either use the UID if passed in at runtime or
# fallback

USER_ID=${UID:-9999}

echo "Executing: [$@]"
if [ "$1" == './docker/php/init.sh' ]; then
    exec "$@"
else
    echo "Starting with UID: $USER_ID"
    useradd --shell /bin/bash -u $USER_ID -o -c "" -m goteo
    usermod -u $USER_ID goteo
    export HOME=/application

    # use umask to allow user remove files if created by a different user
    umask 0
    exec gosu goteo "$@"
fi
