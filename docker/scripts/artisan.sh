#!/bin/bash
set -e

source ${DOCKER_SCRIPT_DIR}/exec.sh $1 $2 php artisan "${@:3}"