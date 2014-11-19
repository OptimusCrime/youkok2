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
        // Generate some filenames
        $special_chars = Utilities::generateUrlFriendly('`foo!"#¤%&/()=?bar');
        $quotes = Utilities::generateUrlFriendly('foo\'bar"bar');
        $with_spaces = Utilities::generateUrlFriendly('foo bar  foo');
        $url_with_spaces = Utilities::generateUrlFriendly('foo bar  foo', true);
        
        // Test them
        $this->assertEquals($special_chars, 'foobar');
        $this->assertEquals($quotes, 'foobarbar');
        $this->assertEquals($with_spaces, 'foo_bar_foo');
        $this->assertEquals($url_with_spaces, 'foo-bar-foo');
    }
}