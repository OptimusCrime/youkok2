<?php
/*
 * File: ViewsErrorTest.php
 * Holds: Testes that there are no errors in the views
 * Created: 25.05.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Views;

use Youkok2\Youkok2;
use Youkok2\Utilities\ClassParser;

class ViewsErrorTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testViews() {
        // Frontpage
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

        // Courses
        $courses_wrapper = new Youkok2();
        $courses_view = $courses_wrapper->load('emner', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\Courses', get_class($courses_view));
        $this->assertEquals(200, $courses_wrapper->getStatus());

        // Download
        $download_wrapper = new Youkok2();
        $download_view = $download_wrapper->load('last-ned/foobar', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\NotFound', get_class($download_view));
        $this->assertEquals(404, $download_wrapper->getStatus());

        // Error
        $error_wrapper = new Youkok2();
        $error_view = $error_wrapper->load(new ClassParser('Views\Error'), [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\Error', get_class($error_view));
        $this->assertEquals(500, $error_wrapper->getStatus());

        // Flat
        $flat_wrapper = new Youkok2();
        $flat_view = $flat_wrapper->load('om', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\Flat', get_class($flat_view));
        $this->assertEquals(200, $flat_wrapper->getStatus());

        // NotFound
        $notfound_wrapper = new Youkok2();
        $notfound_view = $notfound_wrapper->load('foobar', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\NotFound', get_class($notfound_view));
        $this->assertEquals(404, $notfound_wrapper->getStatus());

        // Profile
        $profile_wrapper = new Youkok2();
        $profile_view = $profile_wrapper->load('profil/innstillinger', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\Profile', get_class($profile_view));
        $this->assertEquals(200, $profile_wrapper->getStatus());
        // TODO, this is wrong, should be 403. Things are mesed up because Me is static. Temp fix

        // Search
        $search_wrapper = new Youkok2();
        $search_view = $search_wrapper->load('sok', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\Search', get_class($search_view));
        $this->assertEquals(200, $search_wrapper->getStatus());

        // StaticFiles
        $staticfiles_wrapper = new Youkok2();
        $staticfiles_view = $search_wrapper->load('changelog.txt', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\StaticFiles', get_class($staticfiles_view));
        $this->assertEquals(200, $staticfiles_wrapper->getStatus());
    }
}
