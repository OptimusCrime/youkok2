<?php

namespace Youkok\Tests\Common\Utilities;

use PHPUnit\Framework\TestCase;
use Youkok\Common\Utilities\CacheKeyGenerator;

class CacheKeyGeneratorTest extends TestCase
{
    public function testKeyForElementDownloads(): void
    {
        $this->assertSame(
            'downloads_100',
            CacheKeyGenerator::keyForElementDownloads(100)
        );
    }
}
