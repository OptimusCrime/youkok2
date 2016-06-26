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
use Youkok2\Models\Element;
use Youkok2\Models\Favorite;
use Youkok2\Models\Me;
use Youkok2\Models\Karma;
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
        // Get empty module settings
        $app1 = new Youkok2();
        $me1 = new Me($app1);
        $this->assertNull($me1->getModuleSettings());

        // Get the default module settings for module1/modul2 delta
        $app2 = new Youkok2();
        $me2 = new Me($app2);
        $this->assertEquals($me2->getModuleSettings('module1_delta'), 3);

        // Get empty/none-existing key
        $app3 = new Youkok2();
        $me3 = new Me($app3);
        $this->assertNull($me3->getModuleSettings('foobar'));

        // Get from cookie
        $app4 = new Youkok2();
        $app4->setCookie('module_settings', json_encode(['foo' => 'bar']));
        $me4 = new Me($app4);
        $module_settings_from_cookie_all = $me4->getModuleSettings();
        $this->assertEquals($me4->getModuleSettings('foo'), 'bar');
        $this->assertEquals(gettype($module_settings_from_cookie_all), 'array');
        $this->assertTrue(isset($module_settings_from_cookie_all['foo']));
        $this->assertEquals($module_settings_from_cookie_all['foo'], 'bar');

        // Get from user
        $app5 = new Youkok2();
        $hash = Utilities::hashPassword('foobar', Utilities::generateSalt());
        $user = new User();
        $user->setEmail('foo@bar.com');
        $user->setPassword($hash);
        $user->setModuleSettings(json_encode(['foo' => 'bar']));
        $user->save();
        $app5->setSession('youkok2', Me::generateLoginString($hash, 'foo@bar.com'));
        $me5 = new Me($app5);
        $this->assertEquals($me5->getModuleSettings('foo'), 'bar');
    }

    public function testMeSetModuleSettings() {
        // Set simple module setting to not logged in
        $app1 = new Youkok2();
        $me1 = new Me($app1);
        $me1->setModuleSettings('foo', 'bar');
        $this->assertEquals($me1->getModuleSettings('foo'), 'bar');
        $cookie_content = json_decode($app1->getCookie('module_settings'), true);
        $this->assertEquals($cookie_content['foo'], 'bar');

        // Set module setting to logged in user
        $app2 = new Youkok2();
        $app2->setSession('youkok2', self::createUser('foo@bar.com', 'bar'));

        // Create new Me instance
        $me2 = new Me($app2);
        $me2->setModuleSettings('foo', 'bat');
        $this->assertEquals($me2->getModuleSettings('foo'), 'bat');
        $cookie_content = $app2->getCookie('module_settings');
        $this->assertNull($cookie_content);
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
        // Test lot logged in or anything
        $app1 = new Youkok2();
        $me1 = new Me($app1);
        $this->assertFalse($me1->isLoggedIn());
        $this->assertFalse($me1->isAdmin());
        $this->assertFalse($me1->hasKarma());
        $this->assertFalse($me1->canContribute());

        // Test logged in
        $app2 = new Youkok2();
        $app2->setSession('youkok2', self::createUser('foo2@bar.com', 'bar'));
        $me2 = new Me($app2);
        $this->assertTrue($me2->isLoggedIn());
        $this->assertFalse($me2->isAdmin());
        $this->assertTrue($me2->hasKarma());
        $this->assertTrue($me2->canContribute());

        // Change to admin
        $me2->setId(10000);
        $this->assertTrue($me2->isAdmin());

        // Decrease karma
        $me2->setKarma(0);
        $this->assertFalse($me2->hasKarma());
        $this->assertFalse($me2->canContribute());

        // Test logged in but banned
        $app3 = new Youkok2();
        $app3->setSession('youkok2', self::createUser('foo3@bar.com', 'bar'));
        $me3 = new Me($app3);
        $me3->setBanned(true);
        $this->assertFalse($me3->canContribute());
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
        // Session
        $app1 = new Youkok2();
        $app1->setCookie('youkok2', 'foobar');
        $app1->setSession('youkok2', 'foobar');
        $me1 = new Me($app1);
        $me1->setLogin('hash1', 'email1');
        $this->assertNull($app1->getCookie('youkok2'));
        $this->assertEquals($app1->getSession('youkok2'), Me::generateLoginString('hash1', 'email1'));

        // Cookie
        $app2 = new Youkok2();
        $app2->setCookie('youkok2', 'foobar');
        $app2->setSession('youkok2', 'foobar');
        $me2 = new Me($app1);
        $me2->setLogin('hash2', 'email2', true);
        $this->assertNull($app1->getSession('youkok2'));
        $this->assertEquals($app1->getCookie('youkok2'), Me::generateLoginString('hash2', 'email2'));
    }

    public function testMeGenerateLoginString() {
        $this->assertEquals('barasdkashdsajheeeeehehdffhaaaewwaddaaawwwfoo', Me::generateLoginString('foo', 'bar'));
    }

    public function testMeLogout() {
        // Check that we are redirected if we attempt to log out while not logged in
        $app1 = new Youkok2();
        $me1 = new Me($app1);
        $me1->logOut();
        $this->assertEquals('/', $app1->getHeader('location'));

        // Test successful logout without referer
        $app2 = new Youkok2();
        $app2->setGet('_token', 'foobar');
        $app2->setSession('youkok2', self::createUser('foo@bar.com', 'bar'));
        $me2 = new Me($app2);
        $me2->logOut();
        $this->assertNull($app2->getSession('youkok2'));
        $this->assertEquals('/', $app2->getHeader('location'));

        // Test successful logout with referer
        $app3 = new Youkok2();
        $app3->setGet('_token', 'foobar');
        $app3->setSession('youkok2', self::createUser('foo2@bar.com', 'bar'));
        $app3->setServer('HTTP_REFERER', URL_FULL . 'foobar');
        $me3 = new Me($app3);
        $me3->logOut();
        $this->assertEquals('/foobar', $app3->getHeader('location'));

        // Test login with referer (empty)
        $app4 = new Youkok2();
        $app4->setGet('_token', 'foobar');
        $app4->setSession('youkok2', self::createUser('foo3@bar.com', 'bar'));
        $app4->setServer('HTTP_REFERER', URL_FULL);
        $me4 = new Me($app4);
        $me4->logOut();
        $this->assertEquals('/', $app4->getHeader('location'));
    }

    public function testMeFavorites() {
        // Create hash and email
        $app = new Youkok2();
        $hash = Utilities::hashPassword('foo', Utilities::generateSalt());

        // Create user object
        $user = new User();
        $user->setEmail('foo@bar.com');
        $user->setPassword($hash);
        $user->save();

        // Create the login session
        $token = Me::generateLoginString($hash, 'foo@bar.com');
        $app->setSession('youkok2', $token);

        // Create element
        $element = new Element();
        $element->setUrlFriendly('foo-bar-bat');
        $element->setPending(false);
        $element->setEmpty(false);
        $element->save();

        // Create favorite
        $favorite = new Favorite();
        $favorite->setFile($element->getId());
        $favorite->setUser($user->getId());
        $favorite->save();

        // Create me
        $me = new Me($app);

        // Get all favorites
        $favorites = $me->getFavorites();

        // Make sure we got the favorite we added
        $this->assertEquals($element->getId(), $favorites[0]->getId());

        // Check if is indeed a favorite
        $this->assertFalse($me->isFavorite(99999999));
        $this->assertTrue($me->isFavorite($element->getId()));

        // Fetch by is favorite first
        $app2 = new Youkok2();
        $me2 = new Me($app2);
        $this->assertFalse($me2->isFavorite(999999));
    }

    public function testMeGetKarmaElements() {
        // Create hash and email
        $app = new Youkok2();
        $hash = Utilities::hashPassword('foo', Utilities::generateSalt());

        // Create user object
        $user = new User();
        $user->setEmail('foo@bar.com');
        $user->setPassword($hash);
        $user->save();

        // Create the login session
        $token = Me::generateLoginString($hash, 'foo@bar.com');
        $app->setSession('youkok2', $token);

        // Create karma object
        $karma = new Karma();
        $karma->setUser($user->getId());
        $karma->setFile(3);
        $karma->setValue(10);
        $karma->save();

        // Create me
        $me = new Me($app);

        // Get karma elements
        $karma_objects = $me->getKarmaElements();

        // Make sure we returned the correct karma element
        $this->assertEquals($karma_objects[0]->getId(), $karma->getId());
        $this->assertEquals($karma_objects[0]->getUser(), $user->getId());
    }

    public function testMeUpdateSave() {
        // Update
        $app1 = new Youkok2();
        $app1->setSession('youkok2', self::createUser('foo1@bar.com', 'bar'));
        $me1 = new Me($app1);
        $me1->setNick('foobar');
        $me1->update();

        // Get user from id
        $user1 = new User($me1->getId());
        $this->assertEquals($user1->getNick(), 'foobar');

        // Save
        $app2 = new Youkok2();
        $me2 = new Me($app2);
        $me2->create();
        $me2->setNick('foobat');
        $me2->setPassword('foobar');
        $me2->setEmail('foo@bar.com');
        $me2->save();

        $user2 = new User($me2->getId());
        $this->assertEquals($user2->getNick(), 'foobat');
    }
}
