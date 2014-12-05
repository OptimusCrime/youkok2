echo "Wiping test database"
echo "Todo"

echo ""

echo "Building database"
php vendor/bin/phinx migrate -e test

echo ""

echo "Running tests"
vendor/bin/phpunit -c tests
