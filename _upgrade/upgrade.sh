#!/bin/bash
echo -e "\033[0;33mExecuting git pull\033[0m \n"
git pull --progress

echo -e "\n\033[0;33mExecuting git fetch\033[0m \n"
git fetch --progress

echo -e "\n\033[0;33mClearing cache\033[0m \n"
php "clearcache.class.php"

echo -e "\n\033[0;33mRunning migrations\033[0m \n"
php "migrate.class.php"