<?php
namespace Youkok2\Tests\Utilities;

use Youkok2\Utilities\Utilities;

class UtilitiesTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testPrettifySQLDate() {
        $date1 = Utilities::prettifySQLDate('2014-11-11 12:00:00', false);
        $date2 = Utilities::prettifySQLDate('1999-12-30 21:00:00', false);
        $date3 = Utilities::prettifySQLDate('2000-01-01 00:00:00', false);
        $date4_with_time = Utilities::prettifySQLDate('2000-01-01 00:00:00');
        $date5_with_time = Utilities::prettifySQLDate('2000-01-01 01:01:00');
        $date6_with_time = Utilities::prettifySQLDate('2000-01-01 23:59:00');
        
        $this->assertEquals($date1, '11. nov 2014');
        $this->assertEquals($date2, '30. des 1999');
        $this->assertEquals($date3, '1. jan 2000');
        $this->assertEquals($date4_with_time, '1. jan 2000 @ 00:00:00');
        $this->assertEquals($date5_with_time, '1. jan 2000 @ 01:01:00');
        $this->assertEquals($date6_with_time, '1. jan 2000 @ 23:59:00');
    }
    
    public function testUrlSafe() {
        $special_chars = Utilities::urlSafe('`foo!"#¤%&/()=?bar');
        $quotes = Utilities::urlSafe('foo\'bar"bar');
        $with_spaces = Utilities::urlSafe('foo bar  foo');
        $url_with_spaces = Utilities::urlSafe('foo bar  foo');
        
        $this->assertEquals($special_chars, 'foobar');
        $this->assertEquals($quotes, 'foobarbar');
        $this->assertEquals($url_with_spaces, 'foo-bar-foo');
    }
    
    public function testHashPassword() {
        $password1 = Utilities::hashPassword('foo', 'barasdasdkjhasdjkhasdlkjhasd');
        $password2 = Utilities::hashPassword('foo', 'barasdasdkjhasdjkhasdlkjhasd', false);
        
        $this->assertEquals($password1, '$2y$12$barkebabsdjkhasdeKy3gq3G2zIikAdXB4n.v5E1cv68wsqu6071f11238e' .
            '773ac6bb269ae0a0d4f4bhsleeasdasdkjhayolo');
        $this->assertEquals($password2, '$2y$12$barasdasdkjhasdjkhasdeKy3gq3G2zIikAdXB4n.v5E1cv68wsqu');
    }
    
    public function testPasswordFuckup() {
        $password1 = Utilities::hashPassword('foo', 'barasdasdkjhasdjkhasdlkjhasd');
        $password2 = Utilities::hashPassword('foo', 'barasdasdkjhasdjkhasdlkjhasd', false);
        
        $password_fuckup = Utilities::passwordFuckup($password2);
        
        $this->assertEquals($password1, $password_fuckup);
    }
    
    public function testReversePasswordFuckup() {
        $password1 = Utilities::hashPassword('foo', 'barasdasdkjhasdjkhasdlkjhasd');
        $password2 = Utilities::hashPassword('foo', 'barasdasdkjhasdjkhasdlkjhasd', false);
        
        $reverse_fuckup = Utilities::reverseFuckup($password1);
        
        $this->assertEquals($password2, $reverse_fuckup);
    }
    
    public function testPrettifyFilesize() {
        $size1 = Utilities::prettifyFilesize(10);
        $size2 = Utilities::prettifyFilesize(99999);
        $size3 = Utilities::prettifyFilesize(10000000);
        $size4 = Utilities::prettifyFilesize(999999999);
        $size5 = Utilities::prettifyFilesize(0);
        $size6 = Utilities::prettifyFilesize(-10);

        $this->assertEquals($size1, '10 B');
        $this->assertEquals($size2, '97 kB');
        $this->assertEquals($size3, '9 MB');
        $this->assertEquals($size4, '953 MB');
        $this->assertEquals($size5, '0');
        $this->assertEquals($size6, '-10');
    }

    public function testRandomString() {
        $random = Utilities::generateSalt();
        $random_split = explode('-', $random);
        
        $this->assertEquals(32, strlen($random_split[0]));
        $this->assertEquals(72, strlen($random_split[1]));
    }
}