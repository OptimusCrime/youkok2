<?php
/*
 * File: LoaderTest.php
 * Holds: Testes the Loader class
 * Created: 23.05.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Utilities;

use Youkok2\Utilities\ClassParser;

class ClassParserTest extends \PHPUnit_Framework_TestCase
{
    public function testClassParser() {
        $classParser1 = new ClassParser('foo');
        $classParser2 = new ClassParser('Foo\Bar');
        $classParser3 = new ClassParser('', 'bar');

        // Test views
        $this->assertEquals('\Youkok2\foo', $classParser1->getClass()['view']);
        $this->assertEquals('\Youkok2\Foo\Bar', $classParser2->getClass()['view']);
        $this->assertEquals('\Youkok2\\', $classParser3->getClass()['view']);

        // Test methods
        $this->assertEquals('run', $classParser1->getClass()['method']);
        $this->assertEquals('bar', $classParser3->getClass()['method']);
    }
}
