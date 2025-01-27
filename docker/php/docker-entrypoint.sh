#!/bin/sh
set -e

if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'bin/console' ]; then
    echo "stuff zeofhzeukfzeùfpzfihaz zezffzef" 
	composer install --prefer-dist --no-progress --no-suggest --no-interaction
	php bin/console assets:install --no-interaction

	until php bin/console doctrine:query:sql "select 1" >/dev/null 2>&1; do
	    (>&2 echo "Waiting for PostgreSql to be ready...")
		sleep 1
	done
fi

exec "$@"
