<?php
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
        $hash = Utilities::hashPassword($password, Utilities::generateSalt());

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($hash);
        $user->save();

        return Me::generateLoginString($hash, $email);
    }

    public static function tearDownAfterClass() {
        parent::doTearDownAfterClass();
    }

    public function testFailedLogin() {
        $app1 = new Youkok2();

        $me1 = new Me($app1);
        
        $this->assertFalse($me1->isLoggedIn());

        $app2 = new Youkok2();
        $app2->setSession('youkok2', 'foo');
        $app2->setCookie('youkok2', 'bar');

        $me2 = new Me($app2);

        $this->assertFalse($me2->isLoggedIn());
        $this->assertNull($app2->getSession('youkok2'));
        $this->assertNull($app2->getCookie('youkok2'));
    }

    public function testLoginSession() {
        $app = new Youkok2();
        $app->setSession('youkok2', self::createUser('foo@bar.com', 'bar'));

        $me = new Me($app);

        $this->assertTrue($me->isLoggedIn());
    }

    public function testLoginCookie() {
        $app = new Youkok2();
        $app->setCookie('youkok2', self::createUser('foo@bar.com', 'bar'));

        $me = new Me($app);

        $this->assertTrue($me->isLoggedIn());
    }

    public function testMeCreate() {
        $app = new Youkok2();

        $me = new Me($app);
        $me->create();

        $me->setNick('foobar');

        $this->assertEquals('foobar', $me->getNick());
    }

    public function testMeGetModuleSettings() {
        $app1 = new Youkok2();
        $me1 = new Me($app1);
        $this->assertNull($me1->getModuleSettings());

        $app2 = new Youkok2();
        $me2 = new Me($app2);
        $this->assertEquals($me2->getModuleSettings('module1_delta'), 3);

        $app3 = new Youkok2();
        $me3 = new Me($app3);
        $this->assertNull($me3->getModuleSettings('foobar'));

        $app4 = new Youkok2();
        $app4->setCookie('module_settings', json_encode(['foo' => 'bar']));
        $me4 = new Me($app4);
        $module_settings_from_cookie_all = $me4->getModuleSettings();
        $this->assertEquals($me4->getModuleSettings('foo'), 'bar');
        $this->assertEquals(gettype($module_settings_from_cookie_all), 'array');
        $this->assertTrue(isset($module_settings_from_cookie_all['foo']));
        $this->assertEquals($module_settings_from_cookie_all['foo'], 'bar');

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

        $app6 = new Youkok2();
        $app6->setSession('youkok2', self::createUser('foo6@bar.com', 'bar'));
        $me6 = new Me($app6);
        $me6->getUser()->setModuleSettings(null);
        $this->assertNull($me6->getModuleSettings('foobar'));

        $app7 = new Youkok2();
        $app7->setCookie('module_settings', 10);
        $me7 = new Me($app7);
        $this->assertNull($me7->getModuleSettings('foobar'));

        $app8 = new Youkok2();
        $app8->setCookie('module_settings', json_encode(['foo' => 'bar']));
        $me8 = new Me($app8);
        $this->assertNull($me8->getModuleSettings('bat'));
    }

    public function testMeSetModuleSettings() {
        $app1 = new Youkok2();
        $me1 = new Me($app1);
        $me1->setModuleSettings('foo', 'bar');
        $this->assertEquals($me1->getModuleSettings('foo'), 'bar');
        $cookie_content = json_decode($app1->getCookie('module_settings'), true);
        $this->assertEquals($cookie_content['foo'], 'bar');

        $app2 = new Youkok2();
        $app2->setSession('youkok2', self::createUser('foo@bar.com', 'bar'));

        $me2 = new Me($app2);
        $me2->setModuleSettings('foo', 'bat');
        $this->assertEquals($me2->getModuleSettings('foo'), 'bat');
        $cookie_content = $app2->getCookie('module_settings');
        $this->assertNull($cookie_content);
    }

    public function testMeSetNick() {
        $app = new Youkok2();
        $app->setSession('youkok2', self::createUser('foo@bar.com', 'bar'));

        $me = new Me($app);

        $me->setNick('foobar');

        $this->assertEquals('foobar', $me->getNick());
        $this->assertEquals('foobar', $me->getNick(false));

        $me->setNick('');

        $this->assertNull($me->getNick());
        $this->assertEquals('<em>Anonym</em>', $me->getNick(false));
    }

    public function testMeKarma() {
        $app = new Youkok2();
        $app->setSession('youkok2', self::createUser('foo@bar.com', 'bar'));

        $me = new Me($app);

        $karma = $me->getKarma();
        $karma_pending = $me->getKarmaPending();

        $me->increaseKarma(10);
        $me->increaseKarmaPending(20);

        $this->assertEquals(($karma + 10), $me->getKarma());
        $this->assertEquals(($karma_pending + 20), $me->getKarmaPending());
    }

    public function testMeAccesses() {
        $app1 = new Youkok2();
        $me1 = new Me($app1);
        $this->assertFalse($me1->isLoggedIn());
        $this->assertFalse($me1->isAdmin());
        $this->assertFalse($me1->hasKarma());
        $this->assertFalse($me1->canContribute());

        $app2 = new Youkok2();
        $app2->setSession('youkok2', self::createUser('foo2@bar.com', 'bar'));
        $me2 = new Me($app2);
        $this->assertTrue($me2->isLoggedIn());
        $this->assertFalse($me2->isAdmin());
        $this->assertTrue($me2->hasKarma());
        $this->assertTrue($me2->canContribute());

        $me2->setId(10000);
        $this->assertTrue($me2->isAdmin());

        $me2->setKarma(0);
        $this->assertFalse($me2->hasKarma());
        $this->assertFalse($me2->canContribute());

        $app3 = new Youkok2();
        $app3->setSession('youkok2', self::createUser('foo3@bar.com', 'bar'));
        $me3 = new Me($app3);
        $me3->setBanned(true);
        $this->assertFalse($me3->canContribute());
    }

    public function testMeLogin() {
        $app1 = new Youkok2();
        $me1 = new Me($app1);
        $me1->create();
        $me1->logIn();

        $this->assertNull($app1->getHeader('location'));

        $app2 = new Youkok2();
        $me2 = new Me($app2);
        $app2->setSession('youkok2', self::createUser('foo2@bar.com', 'bar'));
        $me2->logIn();

        $this->assertEquals('/', $app2->getHeader('location'));

        $app3 = new Youkok2();
        $app3->setPost('login-email', 'foo');
        $app3->setPost('login-pw', 'foo');
        $app3->setPost('_token', 'foo');
        $me3 = new Me($app3);
        $me3->logIn();

        $this->assertEquals(400, $app3->getStatus());

        $app4 = new Youkok2();
        $app4->setPost('login-email', 'foo');
        $app4->setPost('login-pw', 'foo');
        $app4->setPost('_token', CsrfManager::getSignature());
        $me4 = new Me($app4);
        $me4->logIn();

        $this->assertEquals('/logg-inn', $app4->getHeader('location'));

        $app5 = new Youkok2();
        self::createUser('foo5@bar.com', 'bar');
        $app5->setPost('login-email', 'foo5@bar.com');
        $app5->setPost('login-pw', 'wrong');
        $app5->setPost('_token', CsrfManager::getSignature());
        $me5 = new Me($app5);
        $me5->logIn();

        $this->assertEquals('/logg-inn', $app5->getHeader('location'));
        $this->assertEquals('foo5@bar.com', $app5->getSession('login_correct_email'));

        $app6 = new Youkok2();
        self::createUser('foo6@bar.com', 'bar');
        $app6->setPost('login-email', 'foo6@bar.com');
        $app6->setPost('login-pw', 'bar');
        $app6->setPost('_token', CsrfManager::getSignature());
        $me6 = new Me($app6);
        $me6->logIn();

        $this->assertEquals('/', $app6->getHeader('location'));
        $this->assertTrue($me6->isLoggedIn());
        $this->assertNotNull($app6->getSession('youkok2'));
        $this->assertNull($app6->getCookie('youkok2'));

        $app7 = new Youkok2();
        self::createUser('foo7@bar.com', 'bar');
        $app7->setPost('login-email', 'foo7@bar.com');
        $app7->setPost('login-pw', 'bar');
        $app7->setPost('login-remember', 'remember');
        $app7->setPost('_token', CsrfManager::getSignature());
        $me7 = new Me($app7);
        $me7->logIn();

        $this->assertNotNull($app7->getCookie('youkok2'));

        $app8 = new Youkok2();
        self::createUser('foo8@bar.com', 'bar');
        $app8->setPost('login-email', 'foo8@bar.com');
        $app8->setPost('login-pw', 'bar');
        $app8->setPost('_token', CsrfManager::getSignature());
        $app8->setServer('HTTP_REFERER', URL_FULL . 'foobar');
        $me8 = new Me($app8);
        $me8->logIn();

        $this->assertEquals('/foobar', $app8->getHeader('location'));

        $app9 = new Youkok2();
        self::createUser('foo9@bar.com', 'bar');
        $app9->setPost('login-email', 'foo9@bar.com');
        $app9->setPost('login-pw', 'bar');
        $app9->setPost('_token', CsrfManager::getSignature());
        $app9->setServer('HTTP_REFERER', URL_FULL);
        $me9 = new Me($app9);
        $me9->logIn();

        $this->assertEquals('/', $app9->getHeader('location'));
    }

    public function testMeSetLogin() {
        $app1 = new Youkok2();
        $app1->setCookie('youkok2', 'foobar');
        $app1->setSession('youkok2', 'foobar');
        $me1 = new Me($app1);
        $me1->setLogin('hash1', 'email1');
        $this->assertNull($app1->getCookie('youkok2'));
        $this->assertEquals($app1->getSession('youkok2'), Me::generateLoginString('hash1', 'email1'));

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
        $app1 = new Youkok2();
        $me1 = new Me($app1);
        $me1->logOut();
        $this->assertEquals('/', $app1->getHeader('location'));

        $app2 = new Youkok2();
        $app2->setGet('_token', 'foobar');
        $app2->setSession('youkok2', self::createUser('foo@bar.com', 'bar'));
        $me2 = new Me($app2);
        $me2->logOut();
        $this->assertNull($app2->getSession('youkok2'));
        $this->assertEquals('/', $app2->getHeader('location'));

        $app3 = new Youkok2();
        $app3->setGet('_token', 'foobar');
        $app3->setSession('youkok2', self::createUser('foo2@bar.com', 'bar'));
        $app3->setServer('HTTP_REFERER', URL_FULL . 'foobar');
        $me3 = new Me($app3);
        $me3->logOut();
        $this->assertEquals('/foobar', $app3->getHeader('location'));

        $app4 = new Youkok2();
        $app4->setGet('_token', 'foobar');
        $app4->setSession('youkok2', self::createUser('foo3@bar.com', 'bar'));
        $app4->setServer('HTTP_REFERER', URL_FULL);
        $me4 = new Me($app4);
        $me4->logOut();
        $this->assertEquals('/', $app4->getHeader('location'));
    }

    public function testMeFavorites() {
        $app = new Youkok2();
        $hash = Utilities::hashPassword('foo', Utilities::generateSalt());

        $user = new User();
        $user->setEmail('foo@bar.com');
        $user->setPassword($hash);
        $user->save();

        $token = Me::generateLoginString($hash, 'foo@bar.com');
        $app->setSession('youkok2', $token);

        $element = new Element();
        $element->setUrlFriendly('foo-bar-bat');
        $element->setPending(false);
        $element->setEmpty(false);
        $element->save();

        $favorite = new Favorite();
        $favorite->setFile($element->getId());
        $favorite->setUser($user->getId());
        $favorite->save();

        $me = new Me($app);

        $favorites = $me->getFavorites();

        $this->assertEquals($element->getId(), $favorites[0]->getId());

        $this->assertFalse($me->isFavorite(99999999));
        $this->assertTrue($me->isFavorite($element->getId()));

        $app2 = new Youkok2();
        $me2 = new Me($app2);
        $this->assertFalse($me2->isFavorite(999999));
    }

    public function testMeGetKarmaElements() {
        $app = new Youkok2();
        $hash = Utilities::hashPassword('foo', Utilities::generateSalt());

        $user = new User();
        $user->setEmail('foo@bar.com');
        $user->setPassword($hash);
        $user->save();

        $token = Me::generateLoginString($hash, 'foo@bar.com');
        $app->setSession('youkok2', $token);

        $karma = new Karma();
        $karma->setUser($user->getId());
        $karma->setFile(3);
        $karma->setValue(10);
        $karma->save();
        
        $me = new Me($app);
        
        $karma_objects = $me->getKarmaElements();

        $this->assertEquals($karma_objects[0]->getId(), $karma->getId());
        $this->assertEquals($karma_objects[0]->getUser(), $user->getId());
    }

    public function testMeUpdateSave() {
        $app1 = new Youkok2();
        $app1->setSession('youkok2', self::createUser('foo1@bar.com', 'bar'));
        $me1 = new Me($app1);
        $me1->setNick('foobar');
        $me1->update();

        $user1 = new User($me1->getId());
        $this->assertEquals($user1->getNick(), 'foobar');

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
