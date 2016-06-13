<?php
/*
 * File: ChangePassword.php
 * Holds: Tests the ChangePassword model
 * Created: 03.11.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Models;

use Youkok2\Models\ChangePassword;

class ChangePasswordTest extends \Youkok2\Tests\YoukokTestCase
{
    public static function tearDownAfterClass() {
        parent::doTearDownAfterClass();
    }

    public function testChangePasswordtSave() {
        // Create new changepassword
        $change_password = new ChangePassword();

        // Set some fields we need
        $change_password->setUser(1);
        $change_password->setHash('foo');
        $change_password->setTimeout(date('Y-m-d G:i:s'));

        // Save object
        $change_password->save();

        // Check that object was saved
        $this->assertTrue(is_numeric($change_password->getId()));
    }

    public function testChangePasswordCreateByHash() {
        // Create new changepassword
        $change_password = new ChangePassword();

        // Set some fields we need
        $change_password->setUser(1);
        $change_password->setHash('foo2');
        $change_password->setTimeout(date('Y-m-d G:i:s'));
        $change_password->save();
        
        // Fetch my hash
        $change_password_fetched = new ChangePassword('foo2');
        
        // Check that we got a object returned
        $this->assertNotNull($change_password_fetched);
    }

    public function testChangePasswordCreateByArray() {
        // Create a new array with the data
        $arr = [
            'id' => 1,
            'user' => 1,
            'hash' => 'foobar',
            'timeout' => 1000
        ];

        // Create new changepassword
        $change_password = new ChangePassword($arr);

        // Check if the array was correctly created
        $this->assertEquals($change_password->getId(), 1);
        $this->assertEquals($change_password->getUser(), 1);
        $this->assertEquals($change_password->getHash(), 'foobar');
        $this->assertEquals($change_password->getTimeout(), 1000);
    }
}
