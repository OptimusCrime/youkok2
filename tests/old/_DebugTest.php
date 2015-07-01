<?php
/*
 * File: DebugTest.php
 * Holds: Debug for now
 * Created: 15.06.2015
 * Project: Youkok2
 */

use \Youkok2\Models\Element as Element;

class DebugTest extends PHPUnit_Framework_TestCase {
    public function testDebugStuff () {
        // Create new element
        $element = new Element();

            // Set some fields we need
        $element->setName('F1337||Foo');
        $element->setUrlFriendly('foo');

        // Save element
        $element->save();

        // Store id
        $id = $element->getId();

        // Add to collection
        ElementCollection::add($element);

        // Get from collection
        $element_reference = ElementCollection::get($id);

        // Update the reference
        $element_reference->setUrlFriendly('bar');

        // Check that the original element got updated
        $this->assertEquals($element_reference->getUrlFriendly(), $element->getUrlFriendly());

    }
} 