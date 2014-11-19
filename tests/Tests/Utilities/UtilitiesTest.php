<?php

use \Youkok2\Utilities\Utilities as Utilities;

/*
 * Test different methods in the Utility class
 */

class UtilitiesTest extends PHPUnit_Framework_TestCase {
    
    /*
     * Make sure urlFriendlies are correct
     */
    
    public function testUrlFriendly() {
        // Generate some urls
        $special_chars = Utilities::generateUrlFriendly('`foo!"#¤%&/()=?bar');
        $quotes = Utilities::generateUrlFriendly('foo\'bar"bar');
        $norwegian = Utilities:generateUrlFriendly('abcæøåabc');
        
        // Test them
        $this->assertEquals($special_chars, 'foobar');
        $this->assertEquals($quotes, 'foobarbar');
        $this->assertEquals($norwegian, 'abcabc');
    }
}