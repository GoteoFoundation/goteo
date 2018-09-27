#!/bin/bash

# Add local user
# Either use the UID if passed in at runtime or
# fallback

USER_ID=${UID:-1000}

echo "Starting with UID : $USER_ID"
useradd --shell /bin/bash -u $USER_ID -o -c "" -m goteo
usermod -u $USER_ID goteo
chown goteo.goteo /application
export HOME=/application

exec gosu goteo "$@"
