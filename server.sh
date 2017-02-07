#! /bin/bash

COMMAND=$1

TEXT_BOLD=$(tput bold)

MESSAGE_ERROR="\033[31mAvailable options ${TEXT_BOLD}start|stop|restart\033[0m"

if [ -z $COMMAND ]; then
	echo -e $MESSAGE_ERROR

	exit
fi

if [ $COMMAND != "start" ] && [ $COMMAND != "stop" ] && [ $COMMAND != "restart" ]; then
	echo -e $MESSAGE_ERROR

	exit
fi

PHP=$(which php)
PHP_VERSION_INFO=$($PHP -v)

ROOT_PATH="$(pwd)"
ROUTER=${ROOT_PATH}"/src/ready.php"

if [ -z $PHP ]; then
	echo -e "\033[31mUnable to find PHP\033[0m"
	exit 1
fi

SWOOLE=$($PHP -m | grep swoole)

if [ -z $SWOOLE ]; then
	echo -e "\033[31mUnable to find Swoole PHP extension\033[0m"
	echo -e "\033[31mInstall Swoole with pecl, Ex: pecl install swoole\033[0m"
	exit 1
fi

MESSAGE_TITLE="\033[31m${TEXT_BOLD}Willer Framework | Created by William Borba - wborba.dev@gmail.com\033[0m"

if [ $COMMAND == "start" ]; then
	echo -e $MESSAGE_TITLE
	echo -e "\033[31mPHP Version...\033[0m"
	echo "------------------------------------------------------"
	echo $PHP_VERSION_INFO
	echo "------------------------------------------------------"
	echo -e "\033[31mServer ready...\033[0m"

	$PHP $ROUTER
fi

if [ $COMMAND == "stop" ]; then
	PID_LIST=$(ps aux | grep 'src/ready.php' | awk '{print $2}')

	PID_TOTAL=($PID_LIST)
	PID_TOTAL=${#PID_TOTAL[@]}

	COUNTER=0

	for PID in $PID_LIST; do
		COUNTER=$((COUNTER+1))

		if [ $COUNTER == $PID_TOTAL ]; then
			break
		fi

		kill -9 $PID

	done

	echo -e "\033[31mServer stoped...\033[0m"
fi

if [ $COMMAND == "restart" ]; then
	echo -e $MESSAGE_TITLE

	PID_LIST=$(ps aux | grep 'src/ready.php' | awk '{print $2}')

	PID_TOTAL=($PID_LIST)
	PID_TOTAL=${#PID_TOTAL[@]}

	COUNTER=0

	for PID in $PID_LIST; do
		COUNTER=$((COUNTER+1))

		if [ $COUNTER == $PID_TOTAL ]; then
			break
		fi

		kill -9 $PID

	done

	echo -e "\033[31mServer stoped...\033[0m"

	echo -e "\033[31mPHP Version...\033[0m"
	echo "------------------------------------------------------"
	echo $PHP_VERSION_INFO
	echo "------------------------------------------------------"
	echo -e "\033[31mServer ready...\033[0m"

	$PHP $ROUTER
fi
