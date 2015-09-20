#!/bin/bash

# Run tests
echo "\033[0;33mRunning tests\033[0m \n"
php vendor/bin/phpunit -c tests

# Newline
echo ""

# Check if we can run cloc
if hash cloc 2>/dev/null; then
    # We can run cloc
    echo "\033[0;33mCounting lines with cloc\033[0m \n"
    cloc --exclude-dir=cache,node_modules,vendor ./
else
    echo "\033[0;33mcloc not installed\033[0m \n"
fi