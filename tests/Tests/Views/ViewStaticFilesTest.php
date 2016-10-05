<?php
namespace Youkok2\Tests\Views;

use Youkok2\Youkok2;

class ViewStaticFilesTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testViewStaticFilesChangelog() {
        $staticfiles_wrapper = new Youkok2();
        $staticfiles_view = $staticfiles_wrapper->load('changelog.txt', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\StaticFiles', get_class($staticfiles_view));
        $this->assertEquals(200, $staticfiles_wrapper->getStatus());
        $this->assertEquals('text/plain; charset=utf-8', $staticfiles_wrapper->getHeader('Content-Type'));
    }

    public function testViewStaticFilesFaviconIco() {
        $staticfiles_wrapper = new Youkok2();
        $staticfiles_view = $staticfiles_wrapper->load('favicon.ico', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\StaticFiles', get_class($staticfiles_view));
        $this->assertEquals(200, $staticfiles_wrapper->getStatus());
        $this->assertGreaterThan(0, $staticfiles_wrapper->getHeader('Content-Length'));
        $this->assertNotNull($staticfiles_wrapper->getStreams()[0]);
        $this->assertEquals('image/x-icon', $staticfiles_wrapper->getHeader('Content-Type'));
    }

    public function testViewStaticFilesFaviconPng() {
        $staticfiles_wrapper = new Youkok2();
        $staticfiles_view = $staticfiles_wrapper->load('favicon.png', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\StaticFiles', get_class($staticfiles_view));
        $this->assertEquals(200, $staticfiles_wrapper->getStatus());
        $this->assertGreaterThan(0, $staticfiles_wrapper->getHeader('Content-Length'));
        $this->assertNotNull($staticfiles_wrapper->getStreams()[0]);
        $this->assertEquals('image/png', $staticfiles_wrapper->getHeader('Content-Type'));
    }
}
