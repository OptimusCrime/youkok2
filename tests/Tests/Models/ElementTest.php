<?php
/*
 * File: ElementTest.php
 * Holds: Tests the Element model
 * Created: 25.05.2015
 * Project: Youkok2
 *
 */

use \Youkok2\Models\Element as Element;

class ElementTest extends PHPUnit_Framework_TestCase {

    /*
     * Test element default values
     */

    public function testElementDefault() {
        // Create new element
        $element = Element::get();

        // Stuff that should be null
        $this->assertNull($element->getId());
        $this->assertNull($element->getName());
        $this->assertNull($element->getUrlFriendly());
        $this->assertNull($element->getOwner());
        $this->assertNull($element->getParent());
        $this->assertNull($element->getChecksum());
        $this->assertNull($element->getChecksum());
        $this->assertNull($element->getSize());
        $this->assertNull($element->getExam());
        $this->assertNull($element->getUrl());

        // Numeric stuff
        $this->assertEquals(1, $element->isEmpty());
        $this->assertEquals(0, $element->getMissingImage());
        $this->assertEquals(0, $element->isDirectory());
        $this->assertEquals(0, $element->isAccepted());
        $this->assertEquals(1, $element->isVisible());
    }

    /*
     * Test element save
     */

    public function testElementSave() {
        // Create new element
        $element = Element::get();

        // Set some fields we need
        $element->setName('F1337||Foo');
        $element->setUrlFriendly('foo');

        // Save element
        $element->save();

        // Check that element was saved
        $this->assertTrue(is_numeric($element->getId()));
    }

    /*
     * Test getter functionality
     */

    public function testElementGet() {
        // Create element
        $element = Element::get();
        $element->setName('Foo1');
        $element->setUrlFriendly('foo1');
        $element->save();

        // Now use the is and make sure we find it in the collection
        $element_collection1 = Element::get($element->getId());
        $this->assertTrue($element_collection1->wasFound());

        // Now check that a element with invalid id was not found
        $element_collection2 = Element::get(-1);
        $this->assertFalse($element_collection2->wasFound());
    }

    /*
     * Test element relationship
     */

    public function testElementRelationship() {
        // Create element1
        $element1 = Element::get();
        $element1->setName('Foo1');
        $element1->setUrlFriendly('foo1');
        $element1->save();

        // Create element2
        $element2 = Element::get();
        $element2->setName('Foo2');
        $element2->setUrlFriendly('foo2');
        $element2->setParent($element1->getId());
        $element2->save();

        // Check that the relationship is true
        $this->assertEquals($element1->getId(), $element2->getParent());
    }
}