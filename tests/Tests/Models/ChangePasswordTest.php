<?php
/*
 * File: ChangePassword.php
 * Holds: Tests the ChangePassword model
 * Created: 03.11.2015
 * Project: Youkok2
 *
 */

use \Youkok2\Models\ChangePassword as ChangePassword;

class ChangePasswordTest extends PHPUnit_Framework_TestCase {
    /*
     * Test object save
     */

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

    /*
     * Test create by hash
     */
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
}