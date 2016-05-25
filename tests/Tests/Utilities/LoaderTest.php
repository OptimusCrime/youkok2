<?php
/*
 * File: LoaderTest.php
 * Holds: Testes the Loader class
 * Created: 25.05.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Tests\Utilities;

use Youkok2\Utilities\Loader;

class LoaderTest extends \Youkok2\Tests\YoukokTestCase
{
    
    /*
     * Test Loader for frontpage
     */
    
    public function testLoaderFrontpage() {
        // Try some paths
        $loader1 = Loader::getClass('');
        $loader2 = Loader::getClass('/');
        $loader3 = Loader::getClass('//');
        
        // Test them
        $this->assertEquals('\Youkok2\Views\Frontpage', $loader1['view']);
        $this->assertEquals('\Youkok2\Views\Frontpage', $loader2['view']);
        $this->assertEquals('\Youkok2\Views\Frontpage', $loader3['view']);
    }
    
    /*
     * Test Loader for 404
     */
    
    public function testLoaderNotFound() {
        // Try some paths
        $loader1 = Loader::getClass('f');
        $loader2 = Loader::getClass('f/');
        $loader3 = Loader::getClass('/f');
        $loader4 = Loader::getClass('//f');
        $loader5 = Loader::getClass('..');
        $loader6 = Loader::getClass('../');
        $loader7 = Loader::getClass('/..');
        $loader8 = Loader::getClass('/../');
        
        // Test them
        $this->assertEquals('\Youkok2\Views\NotFound', $loader1['view']);
        $this->assertEquals('\Youkok2\Views\NotFound', $loader2['view']);
        $this->assertEquals('\Youkok2\Views\NotFound', $loader3['view']);
        $this->assertEquals('\Youkok2\Views\NotFound', $loader4['view']);
        $this->assertEquals('\Youkok2\Views\NotFound', $loader5['view']);
        $this->assertEquals('\Youkok2\Views\NotFound', $loader6['view']);
        $this->assertEquals('\Youkok2\Views\NotFound', $loader7['view']);
        $this->assertEquals('\Youkok2\Views\NotFound', $loader8['view']);
    }
    
    /*
     * Test Loader archive
     */
    
    public function testLoaderArchive() {
        // Try some paths
        $loader1 = Loader::getClass('emner');
        $loader2 = Loader::getClass('emner/');
        $loader3 = Loader::getClass('emner//');
        $loader4 = Loader::getClass('emner/sub');
        $loader5 = Loader::getClass('emner/sub/sub');
        
        // Test them
        $this->assertEquals('\Youkok2\Views\Courses', $loader1['view']);
        $this->assertEquals('\Youkok2\Views\Courses', $loader2['view']);
        $this->assertEquals('\Youkok2\Views\Courses', $loader3['view']);
        
        $this->assertEquals('\Youkok2\Views\Archive', $loader4['view']);
        $this->assertEquals('\Youkok2\Views\Archive', $loader5['view']);
    }
    
    /*
     * Test Loader static routes
     */
    
    public function testLoaderStaticRoutes() {
        // Try some paths
        $loader1 = Loader::getClass('last-ned');
        $loader2 = Loader::getClass('last-ned/');
        
        $loader3 = Loader::getClass('redirect');
        $loader4 = Loader::getClass('redirect/');
        
        // Test them
        $this->assertEquals('\Youkok2\Views\Download', $loader1['view']);
        $this->assertEquals('\Youkok2\Views\Download', $loader2['view']);
        
        $this->assertEquals('\Youkok2\Views\Redirect', $loader3['view']);
        $this->assertEquals('\Youkok2\Views\Redirect', $loader4['view']);
    }
   
   /*
     * Test Loader views with methods
     */
    
    public function testLoaderViewsMethods() {
        // Try some paths
        $loader1 = Loader::getClass('om');
        
        // Test them
        $this->assertEquals('\Youkok2\Views\Flat', $loader1['view']);
        $this->assertEquals('displayAbout', $loader1['method']);
    }

    /**
     * Test loading of processors
     */

    public function testLoaderProcessor() {
        // Try some paths
        $loader1 = Loader::getClass('processor/tasks/clearcache');

        // Test them
        $this->assertEquals('\Youkok2\Processors\Tasks\ClearCache', $loader1['view']);
    }
}
