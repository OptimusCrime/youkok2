<?php
namespace Youkok2\Tests\Utilities;

use Youkok2\Utilities\ClassParser;

class ClassParserTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testClassParser() {
        $classParser1 = new ClassParser('foo');
        $classParser2 = new ClassParser('Foo\Bar');
        $classParser3 = new ClassParser('', 'bar');

        $this->assertEquals('\Youkok2\foo', $classParser1->getClass()['view']);
        $this->assertEquals('\Youkok2\Foo\Bar', $classParser2->getClass()['view']);
        $this->assertEquals('\Youkok2\\', $classParser3->getClass()['view']);

        $this->assertEquals('run', $classParser1->getClass()['method']);
        $this->assertEquals('bar', $classParser3->getClass()['method']);
    }
}
