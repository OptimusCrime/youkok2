<?php
/*
 * File: KarmaTest.php
 * Holds: Tests the Karma model
 * Created: 14.06.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Models;

use Youkok2\Models\Element;
use Youkok2\Models\Karma;

class KarmaTest extends \Youkok2\Tests\YoukokTestCase
{
    public static function tearDownAfterClass() {
        parent::doTearDownAfterClass();
    }

    public function testKarmaDefault() {
        // Create karma
        $karma = new Karma();

        // Assert
        $this->assertNull($karma->getId());
        $this->assertNull($karma->getUser());
        $this->assertNull($karma->getFile());
        $this->assertEquals(5, $karma->getValue());
        $this->assertTrue($karma->isPending());
        $this->assertTrue($karma->getState());
        $this->assertEquals('CURRENT_TIMESTAMP', $karma->getAdded());
    }

    public function testKarmaSave() {
        // Create new karma
        $karma = new Karma();

        // Set some fields we need
        $karma->setUser(1);
        $karma->setFile(2);

        // Save karma
        $karma->save();

        // Check that history was saved
        $this->assertTrue(is_numeric($karma->getId()));
    }

    public function testKarmaGettersSetters() {
        $karma = new Karma();
        $karma->setId(1);
        $karma->setUser(2);
        $karma->setFile(3);
        $karma->setValue(10);
        $karma->setPending(false);
        $karma->setState(false);
        $karma->setAdded('2011-11-11 12:12:12');

        // Check that getters and setters down correctly
        $this->assertEquals(1, $karma->getId());
        $this->assertEquals(2, $karma->getUser());
        $this->assertEquals(3, $karma->getFile());
        $this->assertEquals(10, $karma->getValue());
        $this->assertFalse($karma->isPending());
        $this->assertFalse($karma->getState());
        $this->assertEquals('2011-11-11 12:12:12', $karma->getAdded());
        $this->assertEquals('11. nov 2011 @ 12:12:12', $karma->getAdded(true));
    }

    public function testKarmaGetFile() {
        // Create element
        $element = new Element();
        $element->save();

        // Create karma
        $karma = new Karma();
        $karma->setUser(1);
        $karma->setFile($element->getId());
        $karma->save();

        // Fetch karma
        $karma_fetched = new Karma($karma->getId());

        // Match the element
        $this->assertEquals($element->getId(), $karma_fetched->getFile());
        $this->assertEquals($element->getId(), $karma_fetched->getFile(true)->getId());

    }

    public function testKarmaCreateBy() {
        // By array
        $karma1 = new Karma([
            'id' => 999
        ]);
        $this->assertEquals(999, $karma1->getId());

        // By id
        $karma2 = new Karma();
        $karma2->setUser(1);
        $karma2->setFile(2);
        $karma2->setValue(10);
        $karma2->save();

        // Fetch the saved instance
        $karma_fetched = new Karma($karma2->getId());
        $this->assertEquals($karma2->getId(), $karma_fetched->getId());
        $this->assertEquals(1, $karma_fetched->getUser());
        $this->assertEquals(2, $karma_fetched->getFile());
        $this->assertEquals(10, $karma_fetched->getValue());
    }
}
