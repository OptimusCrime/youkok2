<?php
namespace Youkok2\Tests\Models;

use Youkok2\Models\Message;

class MessageStaticControllerTest extends \Youkok2\Tests\YoukokTestCase
{
    public static function tearDownAfterClass() {
        parent::doTearDownAfterClass();
    }

    public function setUp() {
        parent::doSetUp();
    }

    public function testMessageStaticControllerNoPattern() {
        $message = new Message();
        $message->setTimeStart('1992-01-01 12:12:12');
        $message->setTimeEnd('2055-01-01 12:12:12');
        $message->save();

        $messsages_no_pattern = Message::getMessages('');
        $this->assertEquals(1, count($messsages_no_pattern));

        $messages_frontpage_pattern = Message::getMessages('/');
        $this->assertEquals(1, count($messages_frontpage_pattern));

        $messages_random_pattern = Message::getMessages('/foobar');
        $this->assertEquals(1, count($messages_random_pattern));
    }

    public function testMessageStaticControllerFrontpage() {
        $message = new Message();
        $message->setTimeStart('2000-01-01 12:12:12');
        $message->setTimeEnd('2045-01-01 12:12:12');
        $message->setPattern('/');
        $message->save();

        $messsages_no_pattern = Message::getMessages('about');
        $this->assertEquals(0, count($messsages_no_pattern));

        $messages_frontpage_pattern = Message::getMessages('/');
        $this->assertEquals(1, count($messages_frontpage_pattern));
    }

    public function testMessageStaticControllerSubpage() {
        $message = new Message();
        $message->setTimeStart('2000-01-01 12:12:12');
        $message->setTimeEnd('2055-01-01 12:12:12');
        $message->setPattern('about');
        $message->save();

        $messsages_no_pattern = Message::getMessages('');
        $this->assertEquals(0, count($messsages_no_pattern));
        
        $messages_frontpage_pattern = Message::getMessages('/');
        $this->assertEquals(0, count($messages_frontpage_pattern));

        $messages_correct_pattern = Message::getMessages('about');
        $this->assertEquals(1, count($messages_correct_pattern));
    }

    public function testMessageStaticControllerRegex() {
        $message = new Message();
        $message->setTimeStart('2000-01-01 12:12:12');
        $message->setTimeEnd('2055-01-01 12:12:12');
        $message->setPattern('emner/*');
        $message->save();

        $messsages_no_pattern = Message::getMessages('');
        $this->assertEquals(0, count($messsages_no_pattern));

        $messages_frontpage_pattern = Message::getMessages('emner');
        $this->assertEquals(0, count($messages_frontpage_pattern));

        $messages_correct_pattern = Message::getMessages('emner/foobar');
        $this->assertEquals(1, count($messages_correct_pattern));
    }
}
