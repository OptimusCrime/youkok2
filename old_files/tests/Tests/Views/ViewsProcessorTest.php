<?php
namespace Youkok2\Tests\Views;

use Youkok2\Youkok2;

class ViewsProcessorTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testProcessorView() {
        $processor_wrapper = new Youkok2();
        $processor_wrapper->load('processor/foobar', [
            'close_db' => false,
            'application' => true
        ]);
        $this->assertEquals('application/json', $processor_wrapper->getHeader('Content-Type'));
    }
}