#!/bin/bash

# Get checksum from the user
echo "Enter checksum"
read CHECKSUM

# Do git pull
echo "\033[0;33mExecuting git pull\033[0m \n"
git pull --progress

# Run the upgrade processor
echo "\n\033[0;33mUpgradig JS and CSS\033[0m \n"
php cli/cmd.php tasks/upgrade $CHECKSUM

# Run the clear cache processor
echo "\n\033[0;33mClearing cache\033[0m \n"
php cli/cmd.php tasks/clearcache $CHECKSUM

# Update Composer binary
echo "\n\033[0;33mUpdating composer\033[0m \n"
php composer.phar self-update

# Update Composer dependencies
echo "\n\033[0;33mUpdating composer packages\033[0m \n"
php composer.phar update

# Do Phinx migrations
echo "\n\033[0;33mRunning migrations\033[0m \n"
php vendor/bin/phinx migrate -e production