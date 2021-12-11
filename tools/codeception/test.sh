#!/bin/sh

ENV_CONFIG=""
SUITE=""
TARGET=""
TEST_PATH=""
TOOL_PATH="$(dirname "$(realpath "$0")")"

echo $TOOL_PATH

# Check --env argument
if [ "$1" != "--env" ]
then
    printf "\033[1;31m\"--env\" not set! \nNotice: --env MUST be the first argument. \033[0m\n"
    exit 1;
else
    ENV_CONFIG="$2"
fi

# Check --suite argument
if [ "$3" != "--suite" ]
then
    printf "\033[1;31m\"--suite\" not set! \nNotice: --suite MUST be the second argument. \033[0m\n"
    exit 1;
else
    SUITE="$4"
fi

# Go to acceptance test folder if --suite is "acceptance"
if [ "${SUITE}" = "acceptance" ] ; then
    TEST_PATH=${TOOL_PATH}/../../tests/${SUITE}/BorlabsAcceptance
# Go to wpunit test folder if --suite is "wpunit"
elif [ "${SUITE}" = "wpunit" ] ; then
    TEST_PATH=${TOOL_PATH}/../../tests/${SUITE}/BorlabsWPUnit
else
    printf "\033[1;31m\"--suite ${SUITE}\" not available! \033[0m\n"
    exit 1;
fi

while [ true ] ;
do
    printf "\033[1;96mEnter file/path to analyse, or press return to analyse all:\033[0m\n"

    read -a TARGET

    if [ -f "${TEST_PATH}/${TARGET}" ] || [ -d "${TEST_PATH}/${TARGET}" ] || [ -z ${TARGET} ] ; then
        break
    else
        printf "\033[1;31m\"${TARGET}\" does not exist in \"${TEST_PATH}\"\033[0m\n"
    fi
done

# Go to codeception folder because codeception.dist.yml is located here and required.
cd ${TOOL_PATH} || exit 1;
# Run tests
vendor/bin/codecept run ${SUITE} --env ${ENV_CONFIG} ${TEST_PATH}/${TARGET}
