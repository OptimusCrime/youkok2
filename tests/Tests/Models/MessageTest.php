<?php
namespace Youkok2\Tests\Models;

use Youkok2\Models\Message;

class MessageTest extends \Youkok2\Tests\YoukokTestCase
{
    public static function tearDownAfterClass() {
        parent::doTearDownAfterClass();
    }

    public function testMessageDefault() {
        $message = new Message();

        $this->assertNull($message->getId());
        $this->assertNull($message->getTimeStart());
        $this->assertNull($message->getTimeEnd());
        $this->assertNull($message->getMessage());
        $this->assertEquals('success', $message->getType());
        $this->assertEquals('*', $message->getPattern());
    }

    public function testMessageSave() {
        $message = new Message();

        $message->setTimeStart('2000-01-01 12:12:12');
        $message->setTimeEnd('2001-01-01 12:12:12');

        $message->save();

        $this->assertTrue(is_numeric($message->getId()));
    }

    public function testMessageGettersSetters() {
        $message = new Message();
        $message->setId(1);
        $message->setTimeStart('2000-01-01 12:12:12');
        $message->setTimeEnd('2001-01-01 12:12:12');
        $message->setMessage('foo');
        $message->setType('bar');
        $message->setPattern('/');

        $this->assertEquals(1, $message->getId());
        $this->assertEquals('2000-01-01 12:12:12', $message->getTimeStart());
        $this->assertEquals('2001-01-01 12:12:12', $message->getTimeEnd());
        $this->assertEquals('foo', $message->getMessage());
        $this->assertEquals('bar', $message->getType());
        $this->assertEquals('/', $message->getPattern());
    }

    public function testDownloadCreateBy() {
        $message = new Message([
            'id' => 999
        ]);
        $this->assertEquals(999, $message->getId());

        $message2 = new Message();
        $message2->setTimeStart('2000-01-01 12:12:12');
        $message2->setTimeEnd('2001-01-01 12:12:12');
        $message2->setPattern('foo');
        $message2->setMessage('bar');
        $message2->save();

        $message_fetched = new Message($message2->getId());
        $this->assertEquals($message2->getId(), $message_fetched->getId());
        $this->assertEquals('foo', $message_fetched->getPattern());
        $this->assertEquals('bar', $message_fetched->getMessage());
    }

    public function testDownloadStaticMethods() {
        $message = new Message();
        $message->setTimeStart('2000-01-01 12:12:12');
        $message->setTimeEnd('2035-01-01 12:12:12');
        $message->save();

        $this->assertNotNull(Message::getMessages('*'));
        $this->assertNull(Message::foobar());
    }
}
