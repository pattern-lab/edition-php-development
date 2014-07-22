#!/bin/sh
DIR="$( cd "$( dirname "$0" )" && pwd )"
php "$DIR/../bin/composer.phar" install
php "$DIR/../console" -g