#!/bin/bash
comment_spacer="###########################################"
# docker run -d -p 5080:80 -v /home/wborba/projeto/container/willer/maintenance/docker:/build [image ID] /bin/bash -c "chmod -R 777 /build && ./build/run.sh && service nginx start"

echo $comment_spacer
apt-get autoclean && apt-get update

echo $comment_spacer
DEBIAN_FRONTEND="noninteractive" apt-get install -y vim curl wget

echo $comment_spacer
printf 'deb http://packages.dotdeb.org jessie all' > /etc/apt/sources.list.d/dotdeb.list

echo $comment_spacer
apt-get autoclean && apt-get update

echo $comment_spacer
wget https://www.dotdeb.org/dotdeb.gpg

echo $comment_spacer
apt-key add dotdeb.gpg

echo $comment_spacer
apt-get autoclean && apt-get update

echo $comment_spacer
DEBIAN_FRONTEND="noninteractive" apt-get install -y --force-yes php5-cli php5-fpm php5-mysql php5-pgsql php5-sqlite php5-curl php5-gd php5-mcrypt php5-intl php5-imap php5-tidy

echo $comment_spacer
sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php5/fpm/php.ini

echo $comment_spacer
sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php5/cli/php.ini

echo $comment_spacer
apt-get autoclean && apt-get update

echo $comment_spacer
DEBIAN_FRONTEND="noninteractive" apt-get install -y nginx

echo $comment_spacer
printf "daemon off;" >> /etc/nginx/nginx.conf

echo $comment_spacer
sed -i -e "s/;daemonize\s*=\s*yes/daemonize = no/g" /etc/php5/fpm/php-fpm.conf

echo $comment_spacer
sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php5/fpm/php.ini

echo $comment_spacer
apt-get autoclean

echo $comment_spacer
service nginx status

echo $comment_spacer
service php5-fpm status

echo $comment_spacer
service php5-fpm restart