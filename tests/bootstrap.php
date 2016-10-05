<?php
namespace Youkok2\Tests;

use Youkok2\Utilities\Database;

require_once dirname(__FILE__) . '/TestSettings.php';
require_once BASE_PATH . '/local-default.php';
require_once BASE_PATH . '/index.php';

echo "\n";
echo "\033[32m╔" . str_repeat("═", 67) . "╗\033[0m\n";
echo "\033[32m║                                                                   ║\033[0m\n";
echo "\033[32m║                         Creating directories                      ║\033[0m\n";
echo "\033[32m║                                                                   ║\033[0m\n";
echo "\033[32m╚" . str_repeat("═", 67) . "╝\033[0m\n";
echo "\n\n";

@mkdir(FILE_PATH . '/cache');
@unlink(FILE_PATH . '/db.sqlite3');

$dir = CACHE_PATH;
$it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
$files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
foreach ($files as $file) {
    if ($file->isDir()) {
        rmdir($file->getRealPath());
    }
    else {
        unlink($file->getRealPath());
    }
}
rmdir($dir);

@mkdir(CACHE_PATH);
@mkdir(CACHE_PATH . '/youkok/');

echo "All done" . PHP_EOL . PHP_EOL;

echo "\033[32m╔" . str_repeat("═", 67) . "╗\033[0m\n";
echo "\033[32m║                                                                   ║\033[0m\n";
echo "\033[32m║                     Connecting to database                        ║\033[0m\n";
echo "\033[32m║                                                                   ║\033[0m\n";
echo "\033[32m╚" . str_repeat("═", 67) . "╝\033[0m\n";
echo "\n\n";

Database::connect();

echo "All done" . PHP_EOL . PHP_EOL;

echo "\033[32m╔" . str_repeat("═", 67) . "╗\033[0m\n";
echo "\033[32m║                                                                   ║\033[0m\n";
echo "\033[32m║              Running migrations on test database                  ║\033[0m\n";
echo "\033[32m║                                                                   ║\033[0m\n";
echo "\033[32m╚" . str_repeat("═", 67) . "╝\033[0m\n";
echo "\n\n";

exec('php vendor/bin/phinx migrate -e test', $output);

foreach ($output as $v) {
    echo $v . "\n";
}

echo "All done" . PHP_EOL . PHP_EOL;
