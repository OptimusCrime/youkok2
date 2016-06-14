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
use Youkok2\Utilities\CsrfManager;
use Youkok2\Utilities\Utilities;

class MeTest extends \Youkok2\Tests\YoukokTestCase
{
    private static function createUser($email, $password) {
        // Create hash and email
        $hash = Utilities::hashPassword($password, Utilities::generateSalt());

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

    public function testMeGetModuleSettings() {

    }

    public function testMeSetModuleSettings() {

    }

    public function testMeSetNick() {
        // Create the application
        $app = new Youkok2();
        $app->setSession('youkok2', self::createUser('foo@bar.com', 'bar'));

        // Create new Me instance
        $me = new Me($app);

        // Set nick to real content
        $me->setNick('foobar');

        // Test nick
        $this->assertEquals('foobar', $me->getNick());
        $this->assertEquals('foobar', $me->getNick(false));

        // Reset nick
        $me->setNick('');

        // Make sure nick is correct
        $this->assertNull($me->getNick());
        $this->assertEquals('<em>Anonym</em>', $me->getNick(false));
    }

    public function testMeKarma() {
        // Create the application
        $app = new Youkok2();
        $app->setSession('youkok2', self::createUser('foo@bar.com', 'bar'));

        // Create new Me instance
        $me = new Me($app);

        // Store the current karma
        $karma = $me->getKarma();
        $karma_pending = $me->getKarmaPending();

        // Increase the karma
        $me->increaseKarma(10);
        $me->increaseKarmaPending(20);

        // Make sure the new karmas are correct
        $this->assertEquals(($karma + 10), $me->getKarma());
        $this->assertEquals(($karma_pending + 20), $me->getKarmaPending());
    }

    public function testMeAccesses() {

    }

    public function testMeLogin() {
        // Test login when already logged in
        $app1 = new Youkok2();
        $me1 = new Me($app1);
        $me1->create();
        $me1->logIn();

        // Make sure nothing happens if we are already logged in
        $this->assertNull($app1->getHeader('location'));

        // Test login without any post
        $app2 = new Youkok2();
        $me2 = new Me($app2);
        $app2->setSession('youkok2', self::createUser('foo2@bar.com', 'bar'));
        $me2->logIn();

        // We should be redirected to the from page
        $this->assertEquals('/', $app2->getHeader('location'));

        // Test login with all post (failed CSRF)
        $app3 = new Youkok2();
        $app3->setPost('login-email', 'foo');
        $app3->setPost('login-pw', 'foo');
        $app3->setPost('_token', 'foo');
        $me3 = new Me($app3);
        $me3->logIn();

        // Status code should be 400
        $this->assertEquals(400, $app3->getStatus());

        // Test login with all post (successful CSRF)
        $app4 = new Youkok2();
        $app4->setPost('login-email', 'foo');
        $app4->setPost('login-pw', 'foo');
        $app4->setPost('_token', CsrfManager::getSignature());
        $me4 = new Me($app4);
        $me4->logIn();

        // We should be sent back to the login screen here
        $this->assertEquals('/logg-inn', $app4->getHeader('location'));

        // Test login with wrong password
        $app5 = new Youkok2();
        self::createUser('foo5@bar.com', 'bar');
        $app5->setPost('login-email', 'foo5@bar.com');
        $app5->setPost('login-pw', 'wrong');
        $app5->setPost('_token', CsrfManager::getSignature());
        $me5 = new Me($app5);
        $me5->logIn();

        // We should be sent back to the login screen here
        $this->assertEquals('/logg-inn', $app5->getHeader('location'));
        $this->assertEquals('foo5@bar.com', $app5->getSession('login_correct_email'));

        // Test login with correct password
        $app6 = new Youkok2();
        self::createUser('foo6@bar.com', 'bar');
        $app6->setPost('login-email', 'foo6@bar.com');
        $app6->setPost('login-pw', 'bar');
        $app6->setPost('_token', CsrfManager::getSignature());
        $me6 = new Me($app6);
        $me6->logIn();

        // We should be logged in here
        $this->assertEquals('/', $app6->getHeader('location'));
        $this->assertTrue($me6->isLoggedIn());
        $this->assertNotNull($app6->getSession('youkok2'));
        $this->assertNull($app6->getCookie('youkok2'));

        // Test login with correct password (remember)
        $app7 = new Youkok2();
        self::createUser('foo7@bar.com', 'bar');
        $app7->setPost('login-email', 'foo7@bar.com');
        $app7->setPost('login-pw', 'bar');
        $app7->setPost('login-remember', 'remember');
        $app7->setPost('_token', CsrfManager::getSignature());
        $me7 = new Me($app7);
        $me7->logIn();

        // We should have a cookie here
        $this->assertNotNull($app7->getCookie('youkok2'));

        // Test login with referer (not empty)
        $app8 = new Youkok2();
        self::createUser('foo8@bar.com', 'bar');
        $app8->setPost('login-email', 'foo8@bar.com');
        $app8->setPost('login-pw', 'bar');
        $app8->setPost('_token', CsrfManager::getSignature());
        $app8->setServer('HTTP_REFERER', URL_FULL . 'foobar');
        $me8 = new Me($app8);
        $me8->logIn();

        // Assert correct referer handling
        $this->assertEquals('/foobar', $app8->getHeader('location'));

        // Test login with referer (empty)
        $app9 = new Youkok2();
        self::createUser('foo9@bar.com', 'bar');
        $app9->setPost('login-email', 'foo9@bar.com');
        $app9->setPost('login-pw', 'bar');
        $app9->setPost('_token', CsrfManager::getSignature());
        $app9->setServer('HTTP_REFERER', URL_FULL);
        $me9 = new Me($app9);
        $me9->logIn();

        // Assert correct referer handling
        $this->assertEquals('/', $app9->getHeader('location'));


    }

    public function testMeSetLogin() {

    }

    public function testMeGenerateLoginString() {
        $this->assertEquals('barasdkashdsajheeeeehehdffhaaaewwaddaaawwwfoo', Me::generateLoginString('foo', 'bar'));
    }

    public function testMeLogout() {

    }

    public function testMeGetFavorites() {

    }

    public function testMeIsFavorite() {

    }

    public function testMeGetKarmaElements() {

    }

    public function testMeUpdateSave() {

    }

    public function testMeStaticMethods() {

    }
}
