#!/bin/bash

# Install dependencies
composer install

# Run the migrations
php vendor/bin/phinx migrate -c config/example-phinx.yml