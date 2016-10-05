<?php
namespace Youkok2\Tests\Models;

use Youkok2\Models\History;

class HistoryTest extends \Youkok2\Tests\YoukokTestCase
{
    public static function tearDownAfterClass() {
        parent::doTearDownAfterClass();
    }

    public function testHistoryDefault() {
        $history = new History();

        $this->assertNull($history->getId());
        $this->assertNull($history->getUser());
        $this->assertNull($history->getFile());
        $this->assertNull($history->getHistoryText());
        $this->assertEquals('CURRENT_TIMESTAMP', $history->getAdded());
        $this->assertTrue($history->isVisible());
    }

    public function testHistorySave() {
        $history = new History();

        $history->setUser(1);
        $history->setFile(2);

        $history->save();

        $this->assertTrue(is_numeric($history->getId()));
    }

    public function testHistoryGettersSetters() {
        $history = new History();
        $history->setId(1);
        $history->setUser(2);
        $history->setFile(3);
        $history->setHistoryText('foobar');
        $history->setAdded('2011-11-11 12:12:12');
        $history->setVisible(false);

        $this->assertEquals(1, $history->getId());
        $this->assertEquals(2, $history->getUser());
        $this->assertEquals(3, $history->getFile());
        $this->assertEquals('foobar', $history->getHistoryText());
        $this->assertEquals('2011-11-11 12:12:12', $history->getAdded());
        $this->assertFalse($history->isVisible());
    }

    public function testHistoryCreateBy() {
        $history1 = new History([
            'id' => 999
        ]);
        $this->assertEquals(999, $history1->getId());

        $history2 = new History();
        $history2->setUser(1);
        $history2->setFile(2);
        $history2->setHistoryText('foobar');
        $history2->save();

        $history_fetched = new History($history2->getId());
        $this->assertEquals($history2->getId(), $history_fetched->getId());
        $this->assertEquals(1, $history_fetched->getUser());
        $this->assertEquals(2, $history_fetched->getFile());
        $this->assertEquals('foobar', $history_fetched->getHistoryText());
        $this->assertTrue($history_fetched->isVisible());
    }
}
