<?php
namespace Youkok2\Tests\Models;

use Youkok2\Models\Cache\MeDownloads;

class MeDownloadsTest extends \Youkok2\Tests\YoukokTestCase
{
    public static function tearDownAfterClass() {
        parent::doTearDownAfterClass();
    }

    public function testMeDownloadsDefault() {
        $me_download = new MeDownloads();

        $this->assertNull($me_download->getId());
        $this->assertNull($me_download->getData());
    }

    public function testMeDownloadsCreateById() {
        $me_download = new MeDownloads();
        $me_download->setId(10001);
        $me_download->setData('bat');
        $me_download->save();

        $me_download_fetched = new $me_download($me_download->getId());
        $this->assertEquals(10001, $me_download_fetched->getId());
        $this->assertEquals('bat', $me_download_fetched->getData());
    }

    public function testMeDownloadsCreateByArray() {
        $me_download = new MeDownloads([
            'id' => 99,
            'data' => 'bat'
        ]);

        $this->assertEquals(99, $me_download->getId());
        $this->assertEquals('bat', $me_download->getData());
    }
}
