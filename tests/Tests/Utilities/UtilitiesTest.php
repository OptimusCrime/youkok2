<?php
/*
 * File: UtilitiesTest.php
 * Holds: Testes the Utilities class
 * Created: 25.05.2015
 * Project: Youkok2
 * 
 */

use \Youkok2\Utilities\Utilities as Utilities;

class UtilitiesTest extends PHPUnit_Framework_TestCase {
    
    /*
     * Test prettifySQLDate
     */
    
    public function testPrettifySQLDate() {
        // Generate some dates
        $date1 = Utilities::prettifySQLDate('2014-11-11 12:00:00', false);
        $date2 = Utilities::prettifySQLDate('1999-12-30 21:00:00', false);
        $date3 = Utilities::prettifySQLDate('2000-01-01 00:00:00', false);
        $date4_with_time = Utilities::prettifySQLDate('2000-01-01 00:00:00');
        $date5_with_time = Utilities::prettifySQLDate('2000-01-01 01:01:00');
        $date6_with_time = Utilities::prettifySQLDate('2000-01-01 23:59:00');
        
        // Test them
        $this->assertEquals($date1, '11. nov 2014');
        $this->assertEquals($date2, '30. des 1999');
        $this->assertEquals($date3, '1. jan 2000');
        $this->assertEquals($date4_with_time, '1. jan 2000 @ 00:00:00');
        $this->assertEquals($date5_with_time, '1. jan 2000 @ 01:01:00');
        $this->assertEquals($date6_with_time, '1. jan 2000 @ 23:59:00');
    }
    
    /*
     * Make sure urlSafe is correct
     */
    
    public function testUrlSafe() {
        // Generate some filenames
        $special_chars = Utilities::urlSafe('`foo!"#¤%&/()=?bar');
        $quotes = Utilities::urlSafe('foo\'bar"bar');
        $with_spaces = Utilities::urlSafe('foo bar  foo');
        $url_with_spaces = Utilities::urlSafe('foo bar  foo');
        
        // Test them
        $this->assertEquals($special_chars, 'foobar');
        $this->assertEquals($quotes, 'foobarbar');
        $this->assertEquals($url_with_spaces, 'foo-bar-foo');
    }
    
    /*
     * Test password hashing
     */
    
    public function testHashPassword() {
        // Generate some hashes
        $password1 = Utilities::hashPassword('foo', 'barasdasdkjhasdjkhasdlkjhasd');
        $password2 = Utilities::hashPassword('foo', 'barasdasdkjhasdjkhasdlkjhasd', false);
        
        // Test them
        $this->assertEquals($password1, '$2y$12$barkebabsdjkhasdeKy3gq3G2zIikAdXB4n.v5E1cv68wsqu6071f11238e773ac6bb269ae0a0d4f4bhsleeasdasdkjhayolo');
        $this->assertEquals($password2, '$2y$12$barasdasdkjhasdjkhasdeKy3gq3G2zIikAdXB4n.v5E1cv68wsqu');
    }
    
    /*
     * Test password fuckup
     */
    
    public function testPasswordFuckup() {
        // Generate some hashes
        $password1 = Utilities::hashPassword('foo', 'barasdasdkjhasdjkhasdlkjhasd');
        $password2 = Utilities::hashPassword('foo', 'barasdasdkjhasdjkhasdlkjhasd', false);
        
        $password_fuckup = Utilities::passwordFuckup($password2);
        
        // Test them
        $this->assertEquals($password1, $password_fuckup);
    }
    
    /*
     * Test reverse password fuckup
     */
    
    public function testReversePasswordFuckup() {
        // Generate some hashes
        $password1 = Utilities::hashPassword('foo', 'barasdasdkjhasdjkhasdlkjhasd');
        $password2 = Utilities::hashPassword('foo', 'barasdasdkjhasdjkhasdlkjhasd', false);
        
        $reverse_fuckup = Utilities::reverseFuckup($password1);
        
        // Test them
        $this->assertEquals($password2, $reverse_fuckup);
    }
    
    /*
     * Test pretty filesizes
     */
    
    public function testPrettifyFilesize() {
        $size1 = Utilities::prettifyFilesize(10);
        $size2 = Utilities::prettifyFilesize(99999);
        $size3 = Utilities::prettifyFilesize(10000000);
        $size4 = Utilities::prettifyFilesize(999999999);
        
        // Test them
        $this->assertEquals($size1, '10 B');
        $this->assertEquals($size2, '97 kB');
        $this->assertEquals($size3, '9 MB');
        $this->assertEquals($size4, '953 MB');
    }
}