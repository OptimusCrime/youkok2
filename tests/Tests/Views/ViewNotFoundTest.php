<?php
/*
 * File: ViewNotFoundTest.php
 * Holds: Tests the NotFound view
 * Created: 25.05.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Views;

use Youkok2\Youkok2;
use Youkok2\Utilities\ClassParser;

class ViewNotFoundTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testViewNotFound() {
        // Test the view with calling it with a ClassParser
        $notfound_with_class_wrapper = new Youkok2();
        $notfound_with_view = $notfound_with_class_wrapper->load(new ClassParser('Views\NotFound'));
        $this->assertEquals('Youkok2\Views\NotFound', get_class($notfound_with_view));
        $this->assertEquals(404, $notfound_with_class_wrapper->getStatus());

        // Testing the view with a URL that does not exist
        $notfound_url_wrapper = new Youkok2();
        $notfound_url_view = $notfound_url_wrapper->load('foobar');
        $this->assertEquals('Youkok2\Views\NotFound', get_class($notfound_url_view));
        $this->assertEquals(404, $notfound_url_wrapper->getStatus());

        // Testing with kill option
        $notfound_with_class_kill_wrapper = new Youkok2();
        $notfound_with_class_kill_wrapper->load(new ClassParser('Views\NotFound'), [
            'kill' => true
        ]);
        $this->assertEquals(200, $notfound_with_class_kill_wrapper->getStatus());
    }
}
