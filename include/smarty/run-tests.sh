#!/bin/sh
composer update

php -r 'echo "\nPHP version " . phpversion() . ". ";';

if [ -z $1 ];
then
  echo "Running all unit tests.\n"
  php ./vendor/phpunit/phpunit/phpunit
else
  echo "Running all unit tests, except tests marked with @group $1.\n"
  php ./vendor/phpunit/phpunit/phpunit --exclude-group $1
fi