#!/bin/bash
set -e

DOCKER_FILE_TO_BUILD='Dockerfile'

if [ $D_ENV != 'prod' ]; then
  DOCKER_FILE_TO_BUILD+="-$D_ENV"
fi

echo -e "${YELLOW} Building Docker Image..."
docker build -t $PROJECT_PREFIX:$D_ENV -f ${SCRIPT_DIR}/docker/$DOCKER_FILE_TO_BUILD .
echo -e "${YELLOW} Finished building Docker Image: ${ORANGE} $PROJECT_PREFIX:$D_ENV ${NC}"