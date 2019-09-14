#!/bin/bash
set -e

source ${DOCKER_SCRIPT_DIR}/exec.sh $1 $2 rm -rf vendor
source ${DOCKER_SCRIPT_DIR}/exec.sh $1 $2 composer install