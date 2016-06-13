<?php
/*
 * File: DownloadTest.php
 * Holds: Tests the Download model
 * Created: 14.06.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Models;

use Youkok2\Models\Download;

class DownloadTest extends \Youkok2\Tests\YoukokTestCase
{
    public static function tearDownAfterClass() {
        parent::doTearDownAfterClass();
    }

    public function testDownloadDefault() {
        // Create download
        $download = new Download();

        $this->assertNull($download->getId());
        $this->assertNull($download->getFile());
        $this->assertEquals('CURRENT_TIMESTAMP', $download->getDownloadedTime());
        $this->assertNull($download->getIp());
        $this->assertNull($download->getAgent());
        $this->assertNull($download->getUser());
    }

    public function testDownloadSave() {
        // Create new download
        $download = new Download();

        // Set some fields we need
        $download->setFile(1);

        // Save download
        $download->save();

        // Check that download was saved
        $this->assertTrue(is_numeric($download->getId()));
    }

    public function testDownloadGettersSetters() {
        $download = new Download();
        $download->setId(1);
        $download->setFile(10);
        $download->setDownloadedTime('2000-01-01 12:12:12');
        $download->setIp('8.8.8.8');
        $download->setAgent('Opera');
        $download->setUser(100);

        // Check that getters and setters down correctly
        $this->assertEquals(1, $download->getId());
        $this->assertEquals(10, $download->getFile());
        $this->assertEquals('2000-01-01 12:12:12', $download->getDownloadedTime());
        $this->assertEquals('8.8.8.8', $download->getIp());
        $this->assertEquals('Opera', $download->getAgent());
        $this->assertEquals(100, $download->getUser());
    }

    public function testDownloadCreateBy() {
        // By array
        $download1 = new Download([
            'id' => 999
        ]);
        $this->assertEquals(999, $download1->getId());

        // By id
        $download2 = new Download();
        $download2->setFile(1);
        $download2->setDownloadedTime('2000-01-01 12:12:12');
        $download2->save();

        // Fetch the saved instance
        $download_fetched = new Download($download2->getId());
        $this->assertEquals($download2->getId(), $download_fetched->getId());
    }
}
