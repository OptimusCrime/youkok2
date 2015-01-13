#!/bin/bash
echo "Enter checksum"
read CHECKSUM

echo "\033[0;33mExecuting git pull\033[0m \n"
git pull --progress

echo "\n\033[0;33mExecuting git fetch\033[0m \n"
git fetch --progress
echo "All done."

echo "\n\033[0;33mClearing cache\033[0m \n"
php cli/cmd.php tasks/clearcache $CHECKSUM

echo "\n\033[0;33mUpdating composer\033[0m \n"
php composer.phar self-update

echo "\n\033[0;33mUpdating composer packages\033[0m \n"
php composer.phar update

echo "\n\033[0;33mRunning migrations\033[0m \n"
php vendor/bin/phinx migrate -e production