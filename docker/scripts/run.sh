#!/bin/bash
set -e

echo -e "${CYAN}"
if [ "$(docker ps -aq  -f name=${CONTAINER_NAME})" ]; then
    docker rm -f ${CONTAINER_NAME}
fi

if [ $D_ENV == 'prod' ]; then
  docker run -p ${EXPOSE_PORT}:80 -d --name ${CONTAINER_NAME} ${PROJECT_PREFIX}:${D_ENV}
else
  docker run -v $(pwd):/var/www/html -p ${EXPOSE_PORT}:80 -d --name ${CONTAINER_NAME} ${PROJECT_PREFIX}:${D_ENV}

  source ${DOCKER_SCRIPT_DIR}/exec.sh $1 $2 chmod -R 777 storage
  source ${DOCKER_SCRIPT_DIR}/exec.sh $1 $2 chmod -R 777 bootstrap/cache

  if [ "$RUN_COMPOSER" == "1" ]; then
    source ${DOCKER_SCRIPT_DIR}/composer.sh
  fi
  source ${DOCKER_SCRIPT_DIR}/exec.sh $1 $2 php artisan key:generate
fi

echo -e "${NC}"

echo -e "${GREEN}Project is up and running at ${CYAN}http://localhost:${EXPOSE_PORT}${GREEN} in ${ORANGE}$D_ENV${GREEN} mode.${NC}"