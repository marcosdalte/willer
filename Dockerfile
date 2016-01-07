FROM debian:jessie
MAINTAINER wborba/debian/jessie

RUN apt-get autoclean && apt-get update && \
	DEBIAN_FRONTEND="noninteractive" apt-get install -y vim curl wget git sqlite3 && \
	printf 'deb http://packages.dotdeb.org jessie all' > /etc/apt/sources.list.d/dotdeb.list && \
	apt-get autoclean && apt-get update && \
	wget https://www.dotdeb.org/dotdeb.gpg && \
	apt-key add dotdeb.gpg && \
	apt-get autoclean && apt-get update && \
	DEBIAN_FRONTEND="noninteractive" apt-get install -y --force-yes php5-cli php5-fpm php5-mysql php5-pgsql php5-sqlite php5-curl php5-gd php5-mcrypt php5-intl php5-imap php5-tidy && \
	sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php5/fpm/php.ini && \
	sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php5/cli/php.ini && \
	apt-get autoclean && apt-get update && \
	DEBIAN_FRONTEND="noninteractive" apt-get install -y nginx && \
	printf "daemon off;" >> /etc/nginx/nginx.conf && \
	sed -i -e "s/;daemonize\s*=\s*yes/daemonize = no/g" /etc/php5/fpm/php-fpm.conf && \
	sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php5/fpm/php.ini && \
	apt-get autoclean && \
	service php5-fpm restart && \
	curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer && \
	cd ~ && wget https://phar.phpunit.de/phpunit.phar && chmod +x phpunit.phar && mv phpunit.phar /usr/local/bin/phpunit

EXPOSE 80