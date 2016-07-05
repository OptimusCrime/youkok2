<?php
/*
 * File: MessengeManagerTest.php
 * Holds: Testes the MessengeManager class
 * Created: 05.07.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Utilities;

use Youkok2\Youkok2;
use Youkok2\Utilities\MessageManager;

class MessengeManagerTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testMessages() {
        // Add a single message
        $app = new Youkok2();
        MessageManager::addMessage($app, 'foo1', 'danger', false);
        $this->assertEquals(1, count($app->getSession('youkok2_message')));
        $this->assertEquals('foo1', $app->getSession('youkok2_message')[0]['text']);
        $this->assertEquals('danger', $app->getSession('youkok2_message')[0]['type']);

        // Add new message
        MessageManager::addMessage($app, 'foo2', 'ok', false);
        $this->assertEquals(2, count($app->getSession('youkok2_message')));
        $this->assertEquals('foo2', $app->getSession('youkok2_message')[1]['text']);

        // Add prioritised message
        MessageManager::addMessage($app, 'foo3', 'ok', true);
        $this->assertEquals(3, count($app->getSession('youkok2_message')));
        $this->assertEquals('foo3', $app->getSession('youkok2_message')[0]['text']);
        $this->assertEquals('foo1', $app->getSession('youkok2_message')[1]['text']);
        $this->assertEquals('foo2', $app->getSession('youkok2_message')[2]['text']);
    }

    public function testFileMessages() {
        $app = new Youkok2();
        MessageManager::addFileMessage($app, 'foo');
        MessageManager::addFileMessage($app, 'bar');
        MessageManager::addFileMessage($app, 'bat');
        $this->assertEquals(3, count($app->getSession('youkok2_files')));
        $this->assertEquals('foo', $app->getSession('youkok2_files')[0]);
        $this->assertEquals('bar', $app->getSession('youkok2_files')[1]);
        $this->assertEquals('bat', $app->getSession('youkok2_files')[2]);
    }

    public function testGetMessages() {
        // One message, one file message
        $app1 = new Youkok2();
        MessageManager::addMessage($app1, 'foo1', 'danger', false);
        MessageManager::addFileMessage($app1, 'foo');

        // Get all the messages
        $messages = MessageManager::get($app1, '');

        // Assert
        $this->assertEquals(2, count($messages));
        $this->assertEquals('success', $messages[0]->getType());
        $this->assertContains('foo', $messages[0]->getMessage());
        $this->assertEquals('danger', $messages[1]->getType());
        $this->assertEquals('foo1', $messages[1]->getMessage());

        // Two file messages
        $app2 = new Youkok2();
        MessageManager::addFileMessage($app2, 'foo');
        MessageManager::addFileMessage($app2, 'bar');
        $messages = MessageManager::get($app2, '');
        $this->assertContains('foo', $messages[0]->getMessage());
        $this->assertContains('bar', $messages[0]->getMessage());

        // Three file messages
        $app3 = new Youkok2();
        MessageManager::addFileMessage($app3, 'foo');
        MessageManager::addFileMessage($app3, 'bar');
        MessageManager::addFileMessage($app3, 'bat');
        $messages = MessageManager::get($app3, '');
        $this->assertContains('foo', $messages[0]->getMessage());
        $this->assertContains('bar', $messages[0]->getMessage());
        $this->assertContains('bat', $messages[0]->getMessage());
    }
}
