<?php
/*
 * File: YoukokTestCase.php
 * Holds: Extends the PHPUnit testcase class to make things a bit easier
 * Created: 19.11.2014
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests;

use Youkok2\Youkok2;
use Youkok2\Utilities\Database;

class YoukokTestCase extends \PHPUnit_Framework_TestCase
{
    protected static $doTeardown = false;

    public static function tearDownAfterClass() {
        if (self::$doTeardown) {
            // Construct query
            $query  = "DELETE FROM `archive`;DELETE FROM `changepassword`;DELETE FROM `download`;";
            $query .= "DELETE FROM `favorite`;DELETE FROM `karma`;DELETE FROM `message`;DELETE FROM `user`";

            // Execute query
            Database::$db->exec($query);
        }
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

        // Recreate directories
        @mkdir(CACHE_PATH);
        @mkdir(CACHE_PATH . '/youkok/');
    }
}
