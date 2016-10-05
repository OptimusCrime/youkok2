<?php
namespace Youkok2\Tests\Utilities;

use Youkok2\Models\Element;
use Youkok2\Utilities\CacheManager;

class CacheManagerTest extends \Youkok2\Tests\YoukokTestCase
{
    
    public function testCacheStatus1() {
        $cache1 = CacheManager::isCached(1, 'i');
        $cache2 = CacheManager::isCached(1000, 'i');
        $cache3 = CacheManager::isCached(null, 'i');
        $cache4 = CacheManager::isCached(null, null);
        
        $this->assertFalse($cache1);
        $this->assertFalse($cache2);
        $this->assertFalse($cache3);
        $this->assertFalse($cache4);
    }

    public function testCacheValidy() {
        $cache1 = CacheManager::setCache(1, 'i', ['foo' => 'bar'], true);

        $cache2 = CacheManager::setCache(1, 'i', "foo", true);

        $this->assertTrue($cache1);
        $this->assertFalse($cache2);
    }
    
    public function testCacheStatus2() {
        CacheManager::setCache(1, 'i', ['foo' => 'bar'], true);
        
        $cache1 = CacheManager::isCached(1, 'i');
        $cache2 = CacheManager::isCached(1, null);
        $cache3 = CacheManager::isCached(2, null);
        
        $this->assertTrue($cache1);
        
        $this->assertFalse($cache2);
        $this->assertFalse($cache3);
    }

    public function testCacheStore() {
        CacheManager::setCache(2, 'i', ['foo' => 'bar']);
        
        $cache1 = CacheManager::isCached(2, 'i');
        
        CacheManager::store();
        
        $cache2 = CacheManager::isCached(2, 'i');
        
        $this->assertFalse($cache1);
        $this->assertTrue($cache2);
    }
    
    public function testCacheGet() {
        CacheManager::setCache(9999, 'i', ['foo' => 'bar'], true);
        
        $cache_data1 = CacheManager::getCache(9999, 'i');
        $cache_data2 = CacheManager::getCache(99999, 'i');

        $this->assertTrue(isset($cache_data1['foo']));
        $this->assertEquals('bar', $cache_data1['foo']);
        $this->assertNull($cache_data2);
    }
    
    public function testCacheDelete() {
        CacheManager::setCache(999999, 'i', ['foo' => 'bar'], true);
        CacheManager::deleteCache(999999, 'i');
        
        $cache_status = CacheManager::isCached(999999, 'i');
        
        $this->assertFalse($cache_status);
    }

    public function testCacheCascade() {
        $element = new Element();
        $element->setUrlFriendly('foo');
        $element->setName('foo');
        $element->save();
        
        $element->setName('bar');
        $element->cache();
        
        CacheManager::store();
        
        $element_cache = Element::get($element->getId());
        
        $this->assertEquals($element->getName(), $element_cache->getName());
        
    }
}
