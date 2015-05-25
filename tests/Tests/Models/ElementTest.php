<?php
/*
 * File: ElementTest.php
 * Holds: Tests the Element model
 * Created: 25.05.2015
 * Project: Youkok2
 */

use \Youkok2\Models\Element as Element;

/*
 * Test different methods in the Utility class
 */

class ElementTest extends PHPUnit_Framework_TestCase {
    
    /*
     * Test element default values
     */
    
    public function testElementDefault() {
        // Create new element
        $element = new Element();
        
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
        $element = new Element();
        
        // Set some fields we need
        $element->setName('F1337||Foo');
        $element->setUrlFriendly('foo');
        
        // Save element
        $element->save();
        
        // Check that element was saved
        $this->assertTrue(is_numeric($element->getId()));
    }
    
    /*
     * Test element relationship
     */
    
    public function testElementRelationship() {
        // Create element1
        $element1 = new Element();
        $element1->setName('Foo1');
        $element1->setUrlFriendly('foo1');
        $element1->save();
        
        // Create element1
        $element2 = new Element();
        $element2->setName('Foo2');
        $element2->setUrlFriendly('foo2');
        $element2->setParent($element1->getId());
        $element2->save();
        
        // Check that element was saved
        $this->assertEquals($element1->getId(), $element2->getParent());
    }
}