#!/bin/bash

# Download and install Composer
echo "\033[0;33mDownloading Composer\033[0m \n"
curl -sS https://getcomposer.org/installer | php

# Upgrade Composer with all dependencies
echo "\n\033[0;33mUpdating composer packages\033[0m \n"
php composer.phar install --no-interaction

# Other tings
echo "\n\033[0;33mSetting up stuff\033[0m \n"
cp _scripts/src/install-phinx phinx.yml
cp _scripts/src/install-local-empty local.php