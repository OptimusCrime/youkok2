<?php
namespace Youkok2\Tests\Utilities;

use Youkok2\Utilities\CsrfManager;

class CsrfManagerTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testCsrfManager() {
        $signature1 = CsrfManager::getSignature();
        $this->assertTrue(CsrfManager::validateSignature($signature1));

        $signature2 = CsrfManager::getSignature();
        $this->assertTrue(CsrfManager::validateSignature($signature2));
    }
}
