<?php
/*
 * File: CsrfManagerTest.php
 * Holds: Testes the CsrfManagerTest class
 * Created: 05.07.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Utilities;

use Youkok2\Utilities\CsrfManager;

class CsrfManagerTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testCsrfManager() {
        // Create signer
        $signature1 = CsrfManager::getSignature();
        $this->assertTrue(CsrfManager::validateSignature($signature1));

        // Fetch existing signer
        $signature2 = CsrfManager::getSignature();
        $this->assertTrue(CsrfManager::validateSignature($signature2));
    }
}
