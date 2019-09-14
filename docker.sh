#!/bin/bash
set -e
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
DOCKER_SCRIPT_DIR="${SCRIPT_DIR}/docker/scripts"
source ${DOCKER_SCRIPT_DIR}/colors.sh

D_ENV=$1
D_COMMAND=$2

VALID_VALUES_ENV=("dev" "prod")
VALID_VALUES_COMMAND=("fresh" "build" "run" "exec" "artisan")

IS_VALID='0'
for i in "${VALID_VALUES_ENV[@]}"
do
  if [ "$i" == "$D_ENV" ] ; then
    IS_VALID='1'
  fi
done
if [ "$IS_VALID" == "0" ]; then
	echo -e "${RED}You can only use as first parameter one of: ${VALID_VALUES_ENV[*]}"
	exit 0
fi


IS_VALID='0'
for i in "${VALID_VALUES_COMMAND[@]}"
do
    if [ "$i" == "$D_COMMAND" ] ; then
      IS_VALID='1'
    fi
done
if [ "$IS_VALID" == "0" ]; then
	echo -e "${RED}You can only use as second parameter one of: ${VALID_VALUES_COMMAND[*]}"
	exit 0
fi


if [ -z "$EXPOSE_PORT" ]; then
  EXPOSE_PORT=8080
fi

PROJECT_PREFIX='exams'
CONTAINER_NAME="$PROJECT_PREFIX-$D_ENV"

source ${DOCKER_SCRIPT_DIR}/${D_COMMAND}.sh