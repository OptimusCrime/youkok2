<?php
namespace Youkok2\Tests\Models;

use Youkok2\Models\Element;

class ElementTest extends \Youkok2\Tests\YoukokTestCase
{
    public static function tearDownAfterClass() {
        parent::doTearDownAfterClass();
    }

    public function testElementDefault() {
        $element = Element::get();

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

        $this->assertEquals(1, $element->isEmpty());
        $this->assertEquals(0, $element->getMissingImage());
        $this->assertEquals(0, $element->isDirectory());
        $this->assertEquals(0, $element->isAccepted());
        $this->assertEquals(0, $element->isDeleted());
    }

    public function testElementSave() {
        $element = Element::get();

        $element->setName('F1337||Foo');
        $element->setUrlFriendly('foo');

        $element->save();

        $this->assertTrue(is_numeric($element->getId()));
    }

    public function testElementGet() {
        $element = Element::get();
        $element->setName('Foo1');
        $element->setUrlFriendly('foo1');
        $element->save();

        $element_collection1 = Element::get($element->getId());
        $this->assertTrue($element_collection1->wasFound());

        $element_collection2 = Element::get(-1);
        $this->assertFalse($element_collection2->wasFound());
    }

    public function testElementRelationship() {
        $element1 = Element::get();
        $element1->setName('Foo1');
        $element1->setUrlFriendly('foo1');
        $element1->save();

        $element2 = Element::get();
        $element2->setName('Foo2');
        $element2->setUrlFriendly('foo2');
        $element2->setParent($element1->getId());
        $element2->save();

        $this->assertEquals($element1->getId(), $element2->getParent());

        $this->assertEquals($element1->getId(), $element2->getParent(true)->getId());
    }

    public function testElementGettersSetters() {
        $element_parent = new Element();
        $element_parent->save();

        $element = new Element();
        $element->setId(1);
        $element->setName('foobar');
        $element->setUrlFriendly('foo-bar');
        $element->setOwner(10);
        $element->setParent($element_parent->getId());
        $element->setEmpty(true);
        $element->setChecksum('foo');
        $element->setMimeType('foo/bar');
        $element->setMissingImage(true);
        $element->setSize(100);
        $element->setDirectory(true);
        $element->setPending(true);
        $element->setDeleted(true);
        $element->setExam('2000-01-01 12:12:12');
        $element->setUrl('http://www.google.com');
        $element->setAlias('fuubar');
        $element->setLastVisited('1999-01-01 12:12:12');

        $this->assertEquals(1, $element->getId());
        $this->assertEquals('foobar', $element->getName());
        $this->assertEquals('foo-bar', $element->getUrlFriendly());
        $this->assertEquals(10, $element->getOwner());
        $this->assertEquals($element_parent->getId(), $element->getParent());
        $this->assertEquals($element_parent->getId(), $element->getParent(true)->getId());
        $this->assertTrue($element->isEmpty());
        $this->assertEquals('foo', $element->getChecksum());
        $this->assertEquals('foo/bar', $element->getMimeType());
        $this->assertTrue($element->getMissingImage());
        $this->assertEquals(100, $element->getSize());
        $this->assertEquals('100 B', $element->getSize(true));
        $this->assertTrue($element->isDirectory());
        $this->assertTrue($element->isPending());
        $this->assertTrue($element->isDeleted());
        $this->assertEquals('2000-01-01 12:12:12', $element->getExam());
        $this->assertEquals('1. jan 2000', $element->getExam(true));
        $this->assertEquals('http://www.google.com', $element->getUrl());
        $this->assertEquals('fuubar', $element->getAlias());
        $this->assertEquals('1999-01-01 12:12:12', $element->getLastVisited());
    }

    public function testElementCreateByArray() {
        $element = new Element([
            'id' => 999
        ]);
        $this->assertEquals(999, $element->getId());
    }

    public function testElementCreateByString() {
        $element_new = new Element();
        $element_new->setUrlFriendly('foo-bar-bat');
        $element_new->setPending(false);
        $element_new->setEmpty(false);
        $element_new->save();
        
        $element = new Element('foo-bar-bat');

        $this->assertEquals($element_new->getId(), $element->getId());
    }

    public function testElementStaticMethods() {
        $element = new Element();
        $element->save();

        $this->assertNotNull(Element::get($element->getId()));
        $this->assertNull(Element::foobar());
    }
}
