#!/bin/bash

# Prepares .tmp folder to serve
# runs php-fpm

# Thanks to https://github.com/jasperes/bash-yaml
# Based on https://gist.github.com/pkuczynski/8665367

function parse_yaml() {
    local yaml_file=$1
    local prefix=$2
    local s
    local w
    local fs

    s='[[:space:]]*'
    w='[a-zA-Z0-9_.-]*'
    fs="$(echo @|tr @ '\034')"

    (
        sed -ne '/^--/s|--||g; s|\"|\\\"|g; s/\s*$//g;' \
            -e "/#.*[\"\']/!s| #.*||g; /^#/s|#.*||g;" \
            -e  "s|^\($s\)\($w\)$s:$s\"\(.*\)\"$s\$|\1$fs\2$fs\3|p" \
            -e "s|^\($s\)\($w\)$s[:-]$s\(.*\)$s\$|\1$fs\2$fs\3|p" |

        awk -F"$fs" '{
            indent = length($1)/2;
            if (length($2) == 0) { conj[indent]="+";} else {conj[indent]="";}
            vname[indent] = $2;
            for (i in vname) {if (i > indent) {delete vname[i]}}
                if (length($3) > 0) {
                    vn=""; for (i=0; i<indent; i++) {vn=(vn)(vname[i])("_")}
                    printf("%s%s%s%s=(\"%s\")\n", "'"$prefix"'",vn, $2, conj[indent-1],$3);
                }
            }' |

        sed -e 's/_=/+=/g' \
            -e '/\..*=/s|\.|_|' \
            -e '/\-.*=/s|\-|_|'

    ) < "$yaml_file"
}

function create_variables() {
    local yaml_file="$1"
    eval "$(parse_yaml "$yaml_file")"
}

conf='config/local-docker-settings.yml'

if [ ! -f $conf ]; then
    echo -e "\e[31m$conf does not exists!"
    echo -e "\e[31mAborting"
    exit 1
fi
# read yaml file
create_variables $conf

composer install
npm install
./bin/console migrate install
grunt build:tmp

if [ $? -ne 0 ]; then
    echo -e "\e[31mgrunt build:tmp failed!"
    echo -e "\e[31mAborting"
    exit 1
fi

echo -e "\e[32m*************************************"
echo -e "\e[32m System ready!"
echo
echo -e "\e[32m You can point your browser now to:"
echo -e
echo -e "\e[32m $url__main"
echo -e
echo -e "\e[32m*************************************"

# launch php service
/usr/bin/php-fpm
