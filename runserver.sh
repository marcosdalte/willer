#! /bin/bash

ROOT_PATH="$(pwd)/src"
ROUTER=${ROOT_PATH}"/bootstrap.php"
HOST="0.0.0.0"
PORT="8000"

PHP=$(which php)

if [ $? != 0 ]; then
	echo "Unable to find PHP"
	exit 1
fi

$PHP -S $HOST:$PORT -t $ROOT_PATH $ROUTER
