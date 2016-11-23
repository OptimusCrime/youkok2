#!/bin/bash

# phpcs
php vendor/bin/phpcs

# phpunit and coverage
if [[ "$(php -m)" == *"xdebug"* ]]
then
    # We have xdebug, run with coverage
    php composer.phar phpunit --coverage-clover clover.xml
    php composer.phar coveralls --coverage_clover=clover.xml -v
    php composer.phar phpmd ./src text phpmd
else
    # We do not have xdebug, just run phpunit
    php composer.phar phpunit
    php composer.phar phpmd ./src text phpmd
fi
