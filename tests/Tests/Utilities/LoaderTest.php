<?php
/*
 * File: LoaderTest.php
 * Holds: Testes the Loader class
 * Created: 25.05.2015
 * Project: Youkok2
 * 
 */

use \Youkok2\Utilities\Loader as Loader;
use \Youkok2\Utilities\Routes as Routes;

class LoaderTest extends PHPUnit_Framework_TestCase {
    
    /*
     * Test Loader for frontpage
     */
    
    public function testLoaderFrontpage() {
        // Try some paths
        $loader1 = new Loader('');
        $loader2 = new Loader('/');
        
        // Test them
        $this->assertEquals('\Youkok2\Views\Frontpage', $loader1->getMatch());
        $this->assertEquals('\Youkok2\Views\Frontpage', $loader2->getMatch());
    }
    
    /*
     * Test Loader for 404
     */
    
    public function testLoaderNotFound() {
        // Try some paths
        $loader1 = new Loader('//');
        $loader2 = new Loader('f');
        $loader3 = new Loader('f/');
        $loader4 = new Loader('/f');
        $loader5 = new Loader('//f');
        $loader6 = new Loader('..');
        $loader7 = new Loader('../');
        $loader8 = new Loader('/..');
        $loader9 = new Loader('/../');
        
        // Test them
        $this->assertEquals('\Youkok2\Views\NotFound', $loader1->getMatch());
        $this->assertEquals('\Youkok2\Views\NotFound', $loader2->getMatch());
        $this->assertEquals('\Youkok2\Views\NotFound', $loader3->getMatch());
        $this->assertEquals('\Youkok2\Views\NotFound', $loader4->getMatch());
        $this->assertEquals('\Youkok2\Views\NotFound', $loader5->getMatch());
        $this->assertEquals('\Youkok2\Views\NotFound', $loader6->getMatch());
        $this->assertEquals('\Youkok2\Views\NotFound', $loader7->getMatch());
        $this->assertEquals('\Youkok2\Views\NotFound', $loader8->getMatch());
        $this->assertEquals('\Youkok2\Views\NotFound', $loader9->getMatch());
    }
    
    /*
     * Test Loader archive
     */
    
    public function testLoaderArchive() {
        // Try some paths
        $loader1 = new Loader(Routes::ARCHIVE);
        $loader2 = new Loader(Routes::ARCHIVE . '/');
        $loader3 = new Loader(Routes::ARCHIVE . '//');
        $loader4 = new Loader(Routes::ARCHIVE . '/sub');
        $loader5 = new Loader(Routes::ARCHIVE . '/sub/sub');
        
        // Test them
        $this->assertEquals('\Youkok2\Views\Courses', $loader1->getMatch());
        $this->assertEquals('\Youkok2\Views\Courses', $loader2->getMatch());
        $this->assertEquals('\Youkok2\Views\Courses', $loader3->getMatch());
        
        $this->assertEquals('\Youkok2\Views\Archive', $loader4->getMatch());
        $this->assertEquals('\Youkok2\Views\Archive', $loader5->getMatch());
    }
    
    /*
     * Test Loader static routes
     */
    
    public function testLoaderStaticRoutes() {
        // Try some paths
        $loader1 = new Loader(Routes::DOWNLOAD);
        $loader2 = new Loader(Routes::DOWNLOAD . '/');
        
        $loader3 = new Loader(Routes::REDIRECT);
        $loader4 = new Loader(Routes::REDIRECT . '/');
        
        // Test them
        $this->assertEquals('\Youkok2\Views\Download', $loader1->getMatch());
        $this->assertEquals('\Youkok2\Views\Download', $loader2->getMatch());
        
        $this->assertEquals('\Youkok2\Views\Redirect', $loader3->getMatch());
        $this->assertEquals('\Youkok2\Views\Redirect', $loader4->getMatch());
   }
   
   /*
     * Test Loader views with methods
     */
    
    public function testLoaderViewsMethods() {
        // Try some paths
        $loader1 = new Loader('om');
        
        // Test them
        $this->assertEquals('\Youkok2\Views\Flat.displayAbout', $loader1->getMatch());
   }
}