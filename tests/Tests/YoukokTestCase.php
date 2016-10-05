<?php
namespace Youkok2\Tests;

use Youkok2\Utilities\Database;

class YoukokTestCase extends \PHPUnit_Framework_TestCase
{
    public static function doTearDownAfterClass() {
        $query  = "DELETE FROM `archive`;DELETE FROM `changepassword`;DELETE FROM `download`;";
        $query .= "DELETE FROM `favorite`;DELETE FROM `karma`;DELETE FROM `message`;DELETE FROM `user`;";

        try {
            Database::$db->exec($query);
        }
        catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    public function doSetUp() {
        self::doTearDownAfterClass();
        self::setUpBeforeClass();
    }

    public static function setUpBeforeClass() {
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

        if (Database::$db === null) {
            Database::connect();
        }
    }
}
