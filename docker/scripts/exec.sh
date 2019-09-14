#!/bin/bash
set -e

echo -e "${CYAN}"
if [ $3 == 'bash' ]; then
	docker exec -it ${CONTAINER_NAME} bash
else
	docker exec ${CONTAINER_NAME} "${@:3}"
fi
echo -e "${NC}"