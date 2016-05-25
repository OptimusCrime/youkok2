<?php
/*
 * File: YoukokTestCase.php
 * Holds: Extends the PHPUnit testcase class to make things a bit easier
 * Created: 19.11.2014
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests;

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
}
