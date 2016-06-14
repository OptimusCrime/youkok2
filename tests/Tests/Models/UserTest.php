<?php
/*
 * File: UserTest.php
 * Holds: Tests the User model
 * Created: 14.06.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Models;

use Youkok2\Models\User;

class UserTest extends \Youkok2\Tests\YoukokTestCase
{
    public static function tearDownAfterClass() {
        parent::doTearDownAfterClass();
    }

    public function testUserDefault() {
        // Create user
        $user = new User();

        // Assert
        $this->assertNull($user->getId());
        $this->assertNull($user->getEmail());
        $this->assertNull($user->getPassword());
        $this->assertNull($user->getNick());
        $this->assertNull($user->getModuleSettings());
        $this->assertEquals('CURRENT_TIMESTAMP', $user->getLastSeen());
        $this->assertEquals(5, $user->getKarma());
        $this->assertEquals(0, $user->getKarmaPending());
        $this->assertFalse($user->isBanned());
    }

    public function testUserSave() {
        // Create new user
        $user = new User();

        // Set some fields we need
        $user->setEmail('foo@bar.com');
        $user->setPassword('foobar');

        // Save user
        $user->save();

        // Check that message was saved
        $this->assertTrue(is_numeric($user->getId()));
    }

    public function testUserGettersSetters() {
        $user = new User();
        $user->setId(1);
        $user->setEmail('foo@bar.com');
        $user->setPassword('foobar');
        $user->setNick('foo');
        $user->setModuleSettings('bar');
        $user->setLastSeen('1999-01-01 12:12:12');
        $user->setKarma(10);
        $user->setKarmaPending(15);

        // Check that getters and setters down correctly
        $this->assertEquals(1, $user->getId());
        $this->assertEquals('foo@bar.com', $user->getEmail());
        $this->assertEquals('foobar', $user->getPassword());
        $this->assertEquals('foo', $user->getNick());
        $this->assertEquals('foo', $user->getNick(false));
        $this->assertEquals('1999-01-01 12:12:12', $user->getLastSeen());
        $this->assertEquals(10, $user->getKarma());
        $this->assertEquals(15, $user->getKarmaPending());
    }

    public function testUserGetNick() {
        $user = new User();

        // Test the empty nick setting
        $this->assertNull($user->getNick());
        $this->assertEquals('<em>Anonym</em>', $user->getNick(false));

        // Update nick
        $user->setNick('foo');

        // Test sat nick
        $this->assertEquals('foo', $user->getNick());
        $this->assertEquals('foo', $user->getNick(false));
    }

    public function testUserCreateBy() {
        // By array
        $user1 = new User([
            'id' => 999
        ]);
        $this->assertEquals(999, $user1->getId());

        // By id
        $user2 = new User();
        $user2->setEmail('foo@bar.com');
        $user2->setPassword('foo');
        $user2->setNick('bar');
        $user2->save();

        // Fetch the saved instance
        $user_fetched = new User($user2->getId());
        $this->assertEquals($user2->getId(), $user_fetched->getId());
        $this->assertEquals('foo@bar.com', $user_fetched->getEmail());
        $this->assertEquals('foo', $user_fetched->getPassword());
        $this->assertEquals('bar', $user_fetched->getNick());
    }
}
