<?php
/*
 * File: MeTest.php
 * Holds: Tests the Me model
 * Created: 14.06.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Models;

use Youkok2\Youkok2;
use Youkok2\Models\Me;
use Youkok2\Models\User;
use Youkok2\Utilities\Utilities;

class MeTest extends \Youkok2\Tests\YoukokTestCase
{
    private static function createUser($email, $password) {
        // Create hash and email
        $hash = Utilities::hashPassword('foo', Utilities::generateSalt());

        // Create user object
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($hash);
        $user->save();

        // Create the login session
        return Me::generateLoginString($hash, $email);
    }

    public static function tearDownAfterClass() {
        parent::doTearDownAfterClass();
    }

    public function testFailedLogin() {
        // Create the application
        $app1 = new Youkok2();

        // Create new Me instance
        $me1 = new Me($app1);

        // Make sure that login failed
        $this->assertFalse($me1->isLoggedIn());

        // Create the application
        $app2 = new Youkok2();
        $app2->setSession('youkok2', 'foo');
        $app2->setCookie('youkok2', 'bar');

        // Create new Me instance
        $me2 = new Me($app2);

        // Make sure that login failed
        $this->assertFalse($me2->isLoggedIn());
        $this->assertNull($app2->getSession('youkok2'));
        $this->assertNull($app2->getCookie('youkok2'));
    }

    public function testLoginSession() {
        // Create the application
        $app = new Youkok2();
        $app->setSession('youkok2', self::createUser('foo@bar.com', 'bar'));

        // Create new Me instance
        $me = new Me($app);

        // Make sure that login worked
        $this->assertTrue($me->isLoggedIn());
    }

    public function testLoginCookie() {
        // Create the application
        $app = new Youkok2();
        $app->setCookie('youkok2', self::createUser('foo@bar.com', 'bar'));

        // Create new Me instance
        $me = new Me($app);

        // Make sure that login worked
        $this->assertTrue($me->isLoggedIn());
    }

    public function testMeCreate() {
        // Create the application
        $app = new Youkok2();

        // Create new Me instance
        $me = new Me($app);
        $me->create();

        // Set the nick
        $me->setNick('foobar');

        // Assert that the nick is correct
        $this->assertEquals('foobar', $me->getNick());
    }
}
