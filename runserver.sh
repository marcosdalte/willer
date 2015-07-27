#! /bin/bash

ROOT_PATH="$(pwd)"
ROUTER=${ROOT_PATH}"/server/router.php"
HOST="0.0.0.0"
PORT="8000"

PHP=$(which php)

if [ $? != 0 ]; then
	echo "Unable to find PHP"
	exit 1
fi

mkdir $ROOT_PATH"/server/log"
echo "" > $ROOT_PATH"/server/log/access_log.txt"
chmod -R 0777 ./server/log

$PHP -S $HOST:$PORT -t $ROOT_PATH $ROUTER
