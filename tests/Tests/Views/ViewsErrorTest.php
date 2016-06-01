<?php
/*
 * File: ViewErrorTest.php
 * Holds: Tests the Error view
 * Created: 02.06.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Views;

use Youkok2\Youkok2;
use Youkok2\Utilities\ClassParser;

class ViewErrorTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testViewErrorNoReason() {
        // Error
        $error_wrapper = new Youkok2();
        $error_view = $error_wrapper->load(new ClassParser('Views\Error'), [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\Error', get_class($error_view));
        $this->assertEquals(500, $error_wrapper->getStatus());
    }

    public function testViewErrorDb() {
        // Error
        $error_wrapper = new Youkok2();
        $error_view = $error_wrapper->load(new ClassParser('Views\Error'), [
            'close_db' => false,
            'reason' => 'db'
        ]);
        $this->assertEquals('Youkok2\Views\Error', get_class($error_view));
        $this->assertEquals(503, $error_wrapper->getStatus());
    }

    public function testViewErrorUnavailable() {
        // Error
        $error_wrapper = new Youkok2();
        $error_view = $error_wrapper->load(new ClassParser('Views\Error'), [
            'close_db' => false,
            'reason' => 'unavailable'
        ]);
        $this->assertEquals('Youkok2\Views\Error', get_class($error_view));
        $this->assertEquals(503, $error_wrapper->getStatus());
    }
}
