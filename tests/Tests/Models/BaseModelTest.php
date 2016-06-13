<?php
/*
 * File: BaseModelTest.php
 * Holds: Tests the base model functionality
 * Created: 13.06.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Models;

use Youkok2\Models\Download;

class BaseModelTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testSetDefaults() {
        $download = new Download();

        $this->assertEquals('CURRENT_TIMESTAMP', $download->getDownloadedTime());
        $this->assertNull($download->getAgent());
        $this->assertNull($download->getUser());
    }

    public function testToArrayInitial() {
        $download = new Download();

        // Get the initial array
        $arr = $download->toArrayInitial();

        // Test the values in the array
        $this->assertNull($arr['id']);
        $this->assertNull($arr['file']);
        $this->assertEquals('CURRENT_TIMESTAMP', $arr['downloaded_time']);
        $this->assertNull($arr['ip']);
        $this->assertNull($arr['agent']);
        $this->assertNull($arr['user']);
    }

    public function testGetSchema() {
        $download = new Download();
        $this->assertEquals('array', gettype($download->getSchema()));
    }
}
