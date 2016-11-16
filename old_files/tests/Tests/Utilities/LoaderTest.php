<?php
namespace Youkok2\Tests\Utilities;

use Youkok2\Utilities\Loader;

class LoaderTest extends \Youkok2\Tests\YoukokTestCase
{
    
    public function testLoaderFrontpage() {
        $loader1 = Loader::getClass('');
        $loader2 = Loader::getClass('/');
        $loader3 = Loader::getClass('//');
        
        $this->assertEquals('\Youkok2\Views\Frontpage', $loader1['view']);
        $this->assertEquals('\Youkok2\Views\Frontpage', $loader2['view']);
        $this->assertEquals('\Youkok2\Views\Frontpage', $loader3['view']);
    }
    
    public function testLoaderNotFound() {
        $loader1 = Loader::getClass('f');
        $loader2 = Loader::getClass('f/');
        $loader3 = Loader::getClass('/f');
        $loader4 = Loader::getClass('//f');
        $loader5 = Loader::getClass('..');
        $loader6 = Loader::getClass('../');
        $loader7 = Loader::getClass('/..');
        $loader8 = Loader::getClass('/../');
        
        $this->assertEquals('\Youkok2\Views\NotFound', $loader1['view']);
        $this->assertEquals('\Youkok2\Views\NotFound', $loader2['view']);
        $this->assertEquals('\Youkok2\Views\NotFound', $loader3['view']);
        $this->assertEquals('\Youkok2\Views\NotFound', $loader4['view']);
        $this->assertEquals('\Youkok2\Views\NotFound', $loader5['view']);
        $this->assertEquals('\Youkok2\Views\NotFound', $loader6['view']);
        $this->assertEquals('\Youkok2\Views\NotFound', $loader7['view']);
        $this->assertEquals('\Youkok2\Views\NotFound', $loader8['view']);
    }
    
    public function testLoaderArchive() {
        $loader1 = Loader::getClass('emner');
        $loader2 = Loader::getClass('emner/');
        $loader3 = Loader::getClass('emner//');
        $loader4 = Loader::getClass('emner/sub');
        $loader5 = Loader::getClass('emner/sub/sub');
        
        $this->assertEquals('\Youkok2\Views\Courses', $loader1['view']);
        $this->assertEquals('\Youkok2\Views\Courses', $loader2['view']);
        $this->assertEquals('\Youkok2\Views\Courses', $loader3['view']);
        
        $this->assertEquals('\Youkok2\Views\Archive', $loader4['view']);
        $this->assertEquals('\Youkok2\Views\Archive', $loader5['view']);
    }
    
    public function testLoaderStaticRoutes() {
        $loader1 = Loader::getClass('last-ned');
        $loader2 = Loader::getClass('last-ned/');
        
        $loader3 = Loader::getClass('redirect');
        $loader4 = Loader::getClass('redirect/');
        
        $this->assertEquals('\Youkok2\Views\Download', $loader1['view']);
        $this->assertEquals('\Youkok2\Views\Download', $loader2['view']);
        
        $this->assertEquals('\Youkok2\Views\Redirect', $loader3['view']);
        $this->assertEquals('\Youkok2\Views\Redirect', $loader4['view']);
    }
    
    public function testLoaderViewsMethods() {
        $loader1 = Loader::getClass('om');
        
        $this->assertEquals('\Youkok2\Views\Flat', $loader1['view']);
        $this->assertEquals('displayAbout', $loader1['method']);
    }

    public function testLoaderProcessor() {
        $loader1 = Loader::getClass('processor/tasks/clearcache');
        $loader2 = Loader::getClass('processor/foobar');

        $this->assertEquals('\Youkok2\Processors\Tasks\ClearCache', $loader1['view']);
        $this->assertEquals('\Youkok2\Processors\NotFound', $loader2['view']);
    }

    public function testLoaderRedirect() {
        $loader1 = Loader::getClass('kokeboka/foo');
        $loader2 = Loader::getClass('kokeboka/emner/foo');

        $this->assertNull($loader1['view']);
        $this->assertNull($loader1['method']);
        $this->assertStringEndsWith('/emner/foo', $loader1['redirect']);

        $this->assertNull($loader2['view']);
        $this->assertNull($loader2['method']);
        $this->assertStringEndsWith('/emner/foo', $loader2['redirect']);
    }
}
