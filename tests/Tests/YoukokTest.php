<?php
/*
 * File: YoukokTest.php
 * Holds: Tests the Youkok wrapper
 * Created: 30.05.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests;

use Youkok2\Youkok2;
use Youkok2\Utilities\QueryParser;

class YoukokTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testYoukokEmpty() {
        // Test empty Youkok2 wrapper
        $youkok = new Youkok2();
        $youkok->setInformation();

        $this->assertEmpty($youkok->getBody());
    }

    public function testYoukokGettersAndSetters() {
        // Test empty Youkok2 wrapper
        $youkok = new Youkok2();
        $youkok->setWrapper('foobar');
        $youkok->setHeader('foo', 'bar');
        $youkok->addStream('foobar');
        $youkok->setStatus(404);
        $youkok->setBody('foobar');

        // Assert the getters
        $this->assertEquals('foobar', $youkok->getWrapper());
        $this->assertEquals('bar', $youkok->getHeader('foo'));
        $this->assertEquals('bar', $youkok->getHeaders()['foo']);
        $this->assertNull($youkok->getHeader('bar'));
        $this->assertEquals(1, count($youkok->getStreams()));
        $this->assertEquals('foobar', $youkok->getStreams()[0]);
        $this->assertEquals(404, $youkok->getStatus());
        $this->assertEquals('foobar', $youkok->getBody());
    }

    public function testYoukokSessions() {
        // Create new Youkok2
        $youkok = new Youkok2();

        // Assert missing session
        $this->assertNull($youkok->getSession('foo'));

        // Add session
        $youkok->setSession('foo', 'bar');

        // Assert stored session
        $this->assertEquals('bar', $youkok->getSession('foo'));

        // Assert number of sessions
        $this->assertEquals(1, count($youkok->getSessions()));

        // Remove session
        $youkok->clearSession('foo');

        // Assert removed session
        $this->assertNull($youkok->getSession('foo'));
    }

    public function testYoukokCookies() {
        // Create new Youkok2
        $youkok = new Youkok2();

        // Assert missing cookie
        $this->assertNull($youkok->getCookie('foo'));

        // Add cookie
        $youkok->setCookie('foo', 'bar');

        // Assert stored cookie
        $this->assertEquals('bar', $youkok->getCookie('foo'));

        // Assert number of cookies
        $this->assertEquals(1, count($youkok->getCookies()));

        // Remove cookie
        $youkok->clearCookie('foo');

        // Assert removed cookie
        $this->assertNull($youkok->getCookie('foo'));
    }

    public function testYoukokPost() {
        // Create new Youkok2
        $youkok = new Youkok2();
        $youkok->setPost('foo', 'bar');
        $this->assertEquals('bar', $youkok->getPost('foo'));
        $this->assertNull($youkok->getPost('bat'));
    }

    public function testYoukokGet() {
        // Create new Youkok2
        $youkok = new Youkok2();
        $youkok->setGet('foo', 'bar');
        $this->assertEquals('bar', $youkok->getGet('foo'));
        $this->assertNull($youkok->getGet('bat'));
    }

    public function testYoukokLoading() {
        // Make sure loading works and does not crash if supplied null as settings in load
        $youkok_empty_settings = new Youkok2();
        $youkok_empty_settings->load('', null);
        $this->assertNotNull($youkok_empty_settings);

        // Make sure loading using QueryParser always work
        $youkok_query_parser = new Youkok2();
        $youkok_query_parser->load(new QueryParser(), [
            'close_db' => false
        ]);
        $this->assertNotNull($youkok_query_parser);

        // Make sure loading using QueryParser always work
        $youkok_processor = new Youkok2();
        $youkok_processor->load('processor/foobar');
        $this->assertNotNull($youkok_processor);
    }

    public function testYoukokSend() {
        // Internal send
        $youkok1 = new Youkok2();
        $youkok1->send('foobar');
        $this->assertEquals('/foobar', $youkok1->getHeader('location'));

        // External send
        $youkok2 = new Youkok2();
        $youkok2->send('http://www.google.com', true);
        $this->assertEquals('http://www.google.com', $youkok2->getHeader('location'));

        // Internal send with code
        $youkok3 = new Youkok2();
        $youkok3->send('foobar', false, 301);
        $this->assertEquals('/foobar', $youkok3->getHeader('location'));
        $this->assertEquals(301, $youkok3->getStatus());
    }

    public function testYoukokRedirect() {
        $youkok = new Youkok2();
        $youkok->load('kokeboka/foobar');

        // Make sure our loading turns into a redirect
        $this->assertEquals('http://localhost/emner/foobar', $youkok->getHeader('location'));
        $this->assertEquals(301, $youkok->getStatus());
    }

    public function testYoukokLoadOverwrite() {
        $youkok = new Youkok2();
        $youkok_overwrite_view = $youkok->load('/', [
            'overwrite' => true,
            'overwrite_target' => 'sok',
            'overwrite_settings' => []
        ]);

        // Make sure overwriting the view worked
        $this->assertEquals(200, $youkok->getStatus());
        $this->assertEquals('Youkok2\Views\Search', get_class($youkok_overwrite_view));
    }

    public function testYoukokLoadMethod() {
        // Load without method
        $youkok1 = new Youkok2();
        $youkok1->load('/');
        $this->assertEquals(200, $youkok1->getStatus());

        // Load with method
        $youkok2 = new Youkok2();
        $youkok2->load('changelog.txt');
        $this->assertEquals(200, $youkok2->getStatus());
    }
}
