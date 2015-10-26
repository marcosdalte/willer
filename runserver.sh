#! /bin/bash

ROOT_PATH="$(pwd)"
ROUTER=${ROOT_PATH}"/maintenance/router.php"
HOST="0.0.0.0"
PORT="8000"

PHP=$(which php)

if [ $? != 0 ]; then
	echo "Unable to find PHP"
	exit 1
fi

if [ ! -d $ROOT_PATH"/maintenance/log" ]; then
	mkdir $ROOT_PATH"/maintenance/log"
	chmod -R 0777 ./maintenance/log
	echo '' > $ROOT_PATH"/maintenance/log/error_log.txt"
fi

$PHP -S $HOST:$PORT -t $ROOT_PATH $ROUTER
