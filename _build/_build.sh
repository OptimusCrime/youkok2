#!/bin/bash
echo "Enter checksum"
read CHECKSUM

echo "\033[0;33mBuilding CSS and JS files\033[0m \n"
php cli/cmd.php tasks/build $CHECKSUM

echo "\033[0;33mRunning tests\033[0m \n"
php vendor/bin/phpunit -c tests
