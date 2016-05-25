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

class ViewsErrorTest extends \PHPUnit_Framework_TestCase
{
    public function testViews() {
        // Frontpage
        /*$frontpage_wrapper = new Youkok2();
        $frontpage_view = $frontpage_wrapper->load('');
        $this->assertEquals('Youkok2\Views\Frontpage', get_class($frontpage_view));
        $this->assertEquals(200, $frontpage_wrapper->getStatus());
        */
        // Archive (note that this will return 404 because we don't have the actual courses yet)
        $archive_wrapper = new Youkok2();
        $archive_view = $archive_wrapper->load('emner/foobar');
        $this->assertEquals('Youkok2\Views\NotFound', get_class($archive_view));
        $this->assertEquals(404, $archive_wrapper->getStatus());

        // Courses
        $courses_wrapper = new Youkok2();
        $courses_view = $courses_wrapper->load('emner');
        $this->assertEquals('Youkok2\Views\Courses', get_class($courses_view));
        $this->assertEquals(200, $courses_wrapper->getStatus());

        // Download
        $download_wrapper = new Youkok2();
        $download_view = $download_wrapper->load('last-ned/foobar');
        $this->assertEquals('Youkok2\Views\NotFound', get_class($download_view));
        $this->assertEquals(404, $download_wrapper->getStatus());

        // Error
        $error_wrapper = new Youkok2();
        $error_view = $error_wrapper->load(new ClassParser('Views\Error'));
        $this->assertEquals('Youkok2\Views\Error', get_class($error_view));
        $this->assertEquals(500, $error_wrapper->getStatus());

        // Flat
        $flat_wrapper = new Youkok2();
        $flat_view = $flat_wrapper->load('om');
        $this->assertEquals('Youkok2\Views\Flat', get_class($flat_view));
        $this->assertEquals(200, $flat_wrapper->getStatus());

        // NotFound
        $notfound_wrapper = new Youkok2();
        $notfound_view = $notfound_wrapper->load('foobar');
        $this->assertEquals('Youkok2\Views\NotFound', get_class($notfound_view));
        $this->assertEquals(404, $notfound_wrapper->getStatus());

        // Profile
        $profile_wrapper = new Youkok2();
        $profile_view = $profile_wrapper->load('profil/innstillinger');
        $this->assertEquals('Youkok2\Views\Profile', get_class($profile_view));
        $this->assertEquals(403, $profile_wrapper->getStatus());
    }
}
