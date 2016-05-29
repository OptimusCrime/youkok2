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
}
