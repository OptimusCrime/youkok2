<?php
namespace Youkok2\Tests\Models;

use Youkok2\Models\ChangePassword;

class ChangePasswordTest extends \Youkok2\Tests\YoukokTestCase
{
    public static function tearDownAfterClass() {
        parent::doTearDownAfterClass();
    }

    public function testChangePasswordtSave() {
        $change_password = new ChangePassword();

        $change_password->setUser(1);
        $change_password->setHash('foo');
        $change_password->setTimeout(date('Y-m-d G:i:s'));

        $change_password->save();

        $this->assertTrue(is_numeric($change_password->getId()));
    }

    public function testChangePasswordCreateByHash() {
        $change_password = new ChangePassword();

        $change_password->setUser(1);
        $change_password->setHash('foo2');
        $change_password->setTimeout(date('Y-m-d G:i:s'));
        $change_password->save();
        
        $change_password_fetched = new ChangePassword('foo2');
        
        $this->assertNotNull($change_password_fetched);
    }

    public function testChangePasswordCreateByArray() {
        $arr = [
            'id' => 1,
            'user' => 1,
            'hash' => 'foobar',
            'timeout' => 1000
        ];

        $change_password = new ChangePassword($arr);

        $this->assertEquals($change_password->getId(), 1);
        $this->assertEquals($change_password->getUser(), 1);
        $this->assertEquals($change_password->getHash(), 'foobar');
        $this->assertEquals($change_password->getTimeout(), 1000);
    }
}
