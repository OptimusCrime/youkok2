<?php
namespace Youkok2\Tests\Views;

use Youkok2\Youkok2;
use Youkok2\Utilities\ClassParser;

class ViewsTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testViews() {
        $frontpage_wrapper = new Youkok2();
        $frontpage_view = $frontpage_wrapper->load('', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\Frontpage', get_class($frontpage_view));
        $this->assertEquals(200, $frontpage_wrapper->getStatus());

        // Archive (note that this will return 404 because we don't have the actual courses yet)
        $archive_wrapper = new Youkok2();
        $archive_view = $archive_wrapper->load('emner/foobar', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\NotFound', get_class($archive_view));
        $this->assertEquals(404, $archive_wrapper->getStatus());

        $courses_wrapper = new Youkok2();
        $courses_view = $courses_wrapper->load('emner', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\Courses', get_class($courses_view));
        $this->assertEquals(200, $courses_wrapper->getStatus());

        $download_wrapper = new Youkok2();
        $download_view = $download_wrapper->load('last-ned/foobar', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\NotFound', get_class($download_view));
        $this->assertEquals(404, $download_wrapper->getStatus());

        $notfound_wrapper = new Youkok2();
        $notfound_view = $notfound_wrapper->load('foobar', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\NotFound', get_class($notfound_view));
        $this->assertEquals(404, $notfound_wrapper->getStatus());

        $profile_wrapper = new Youkok2();
        $profile_view = $profile_wrapper->load('profil/innstillinger', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\Profile', get_class($profile_view));
        $this->assertEquals(403, $profile_wrapper->getStatus());

        $search_wrapper = new Youkok2();
        $search_view = $search_wrapper->load('sok', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\Search', get_class($search_view));
        $this->assertEquals(200, $search_wrapper->getStatus());
    }
}
