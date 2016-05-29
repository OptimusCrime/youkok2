#!/bin/bash

# phpcs
php vendor/bin/phpcs

# phpunit and coverage
if [[ "$(php -m)" == *"xdebug"* ]]
then
    # We have xdebug, run with coverage
    php vendor/bin/phpunit --coverage-clover clover.xml
    php vendor/bin/coveralls --coverage_clover=clover.xml -v
else
    # We do not have xdebug, just run phpunit
    php vendor/bin/phpunit
fi
