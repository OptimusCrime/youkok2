#!/bin/bash

# Download and install Composer
echo "\033[0;33mDownloading Composer\033[0m \n"
#curl -sS https://getcomposer.org/installer | php

# Upgrade Composer with all dependencies
echo "\n\033[0;33mUpdating composer packages\033[0m \n"
#php composer.phar update

# Running the installation script
echo "\n\033[0;33mRunning installation script\033[0m \n"
php _scripts/src/installer.php