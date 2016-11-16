<?php
namespace Youkok2\Tests\Views;

use Youkok2\Youkok2;
use Youkok2\Utilities\ClassParser;

class ViewErrorTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testViewErrorNoReason() {
        $error_wrapper = new Youkok2();
        $error_view = $error_wrapper->load(new ClassParser('Views\Error'), [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\Error', get_class($error_view));
        $this->assertEquals(500, $error_wrapper->getStatus());
    }

    public function testViewErrorDb() {
        $error_wrapper = new Youkok2();
        $error_view = $error_wrapper->load(new ClassParser('Views\Error'), [
            'close_db' => false,
            'reason' => 'db'
        ]);
        $this->assertEquals('Youkok2\Views\Error', get_class($error_view));
        $this->assertEquals(503, $error_wrapper->getStatus());
    }

    public function testViewErrorUnavailable() {
        $error_wrapper = new Youkok2();
        $error_view = $error_wrapper->load(new ClassParser('Views\Error'), [
            'close_db' => false,
            'reason' => 'unavailable'
        ]);
        $this->assertEquals('Youkok2\Views\Error', get_class($error_view));
        $this->assertEquals(503, $error_wrapper->getStatus());
    }
}
