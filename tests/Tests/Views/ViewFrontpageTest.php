<?php
/*
 * File: ViewFrontpageTest.php
 * Holds: Tests the Frontpage view
 * Created: 25.05.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Views;

use Youkok2\Youkok2;
use Youkok2\Models\Me;
use Youkok2\Models\User;
use Youkok2\Utilities\Utilities;

class ViewFrontpageTest extends \Youkok2\Tests\YoukokTestCase
{
    public static function tearDownAfterClass() {
        parent::doTearDownAfterClass();
    }

    public function testViewFrontpageKill() {
        // Create the wrapper
        $frontpage_wrapper = new Youkok2();
        $frontpage_wrapper->load('', [
            'kill' => true,
            'close_db' => false
        ]);
        $this->assertEquals(200, $frontpage_wrapper->getStatus());
    }

    public function testViewFrontpageLoggedIn() {
        // Create hash and email
        $email = 'foo@bar.com';
        $hash = Utilities::hashPassword('foo', Utilities::generateSalt());

        // Create user object
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($hash);
        $user_save = $user->save();

        // Create the wrapper
        $frontpage_wrapper = new Youkok2();

        // Initiate login
        $login_token = Me::generateLoginString($hash, $email);
        $frontpage_wrapper->setSession('youkok2', $login_token);

        // Load the frontpage
        $frontpage_wrapper->load('', [
            'close_db' => false
        ]);

        // Assert things
        $this->assertEquals(true, $user_save);
        $this->assertNull($user->getLastError());
        $this->assertEquals(200, $frontpage_wrapper->getStatus());
    }
}
