<?php
namespace Youkok2\Tests;

use Youkok2\Youkok2;
use Youkok2\Utilities\QueryParser;

class YoukokTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testYoukokEmpty() {
        $youkok = new Youkok2();
        $youkok->setInformation();

        $this->assertEmpty($youkok->getBody());
    }

    public function testYoukokGettersAndSetters() {
        $youkok = new Youkok2();
        $youkok->setWrapper('foobar');
        $youkok->setHeader('foo', 'bar');
        $youkok->addStream('foobar');
        $youkok->setStatus(404);
        $youkok->setBody('foobar');

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
        $youkok = new Youkok2();
        
        $this->assertNull($youkok->getSession('foo'));

        $youkok->setSession('foo', 'bar');

        $this->assertEquals('bar', $youkok->getSession('foo'));

        $this->assertEquals(1, count($youkok->getSessions()));

        $youkok->clearSession('foo');

        $this->assertNull($youkok->getSession('foo'));
    }

    public function testYoukokCookies() {
        $youkok = new Youkok2();

        $this->assertNull($youkok->getCookie('foo'));

        $youkok->setCookie('foo', 'bar');

        $this->assertEquals('bar', $youkok->getCookie('foo'));

        $this->assertEquals(1, count($youkok->getCookies()));

        $youkok->clearCookie('foo');

        $this->assertNull($youkok->getCookie('foo'));
    }

    public function testYoukokPost() {
        $youkok = new Youkok2();
        $youkok->setPost('foo', 'bar');
        $this->assertEquals('bar', $youkok->getPost('foo'));
        $this->assertNull($youkok->getPost('bat'));
    }

    public function testYoukokGet() {
        $youkok = new Youkok2();
        $youkok->setGet('foo', 'bar');
        $this->assertEquals('bar', $youkok->getGet('foo'));
        $this->assertNull($youkok->getGet('bat'));
    }

    public function testYoukokLoading() {
        $youkok_empty_settings = new Youkok2();
        $youkok_empty_settings->load('', null);
        $this->assertNotNull($youkok_empty_settings);

        $youkok_query_parser = new Youkok2();
        $youkok_query_parser->setGet('q', '');
        $youkok_query_parser->load(new QueryParser($youkok_query_parser), [
            'close_db' => false
        ]);
        $this->assertNotNull($youkok_query_parser);
        
        $youkok_processor = new Youkok2();
        $youkok_processor->load('processor/foobar');
        $this->assertNotNull($youkok_processor);
    }

    public function testYoukokSend() {
        $youkok1 = new Youkok2();
        $youkok1->send('foobar');
        $this->assertEquals('/foobar', $youkok1->getHeader('location'));

        $youkok2 = new Youkok2();
        $youkok2->send('http://www.google.com', true);
        $this->assertEquals('http://www.google.com', $youkok2->getHeader('location'));

        $youkok3 = new Youkok2();
        $youkok3->send('foobar', false, 301);
        $this->assertEquals('/foobar', $youkok3->getHeader('location'));
        $this->assertEquals(301, $youkok3->getStatus());
    }

    public function testYoukokRedirect() {
        $youkok = new Youkok2();
        $youkok->load('kokeboka/foobar');

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

        $this->assertEquals(200, $youkok->getStatus());
        $this->assertEquals('Youkok2\Views\Search', get_class($youkok_overwrite_view));
    }

    public function testYoukokLoadMethod() {
        $youkok1 = new Youkok2();
        $youkok1->load('/');
        $this->assertEquals(200, $youkok1->getStatus());

        $youkok2 = new Youkok2();
        $youkok2->load('changelog.txt');
        $this->assertEquals(200, $youkok2->getStatus());
    }
}
