#!/bin/bash
echo "\033[0;33mDownloading Composer\033[0m \n"
curl -sS https://getcomposer.org/installer | php

echo "\n\033[0;33mUpdating composer packages\033[0m \n"
php composer.phar update