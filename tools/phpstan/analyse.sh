#!/bin/sh

TOOL_PATH="$(dirname "$(realpath "$0")")"
TARGET=""

# Go to source code folder
cd ${TOOL_PATH}/../../html/wp-content/plugins/borlabs-cookie/classes || exit 1;

while [ true ] ;
do
    printf "\033[1;96mEnter file/path to analyse, or press return to analyse all:\033[0m\n"

    read -a TARGET

    if [ -f "${TARGET}" ] || [ -d "${TARGET}" ] ; then
        break
    elif [ -z ${TARGET} ] ; then
        TARGET="*"
        break
    else
        printf "\033[1;31m\"${TARGET}\" does not exist in \"$(pwd)\"\033[0m\n"
    fi
done

php -d memory_limit=2000M ${TOOL_PATH}/vendor/bin/phpstan analyse ${TARGET}
