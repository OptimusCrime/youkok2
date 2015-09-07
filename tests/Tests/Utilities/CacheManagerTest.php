<?php
/*
 * File: CacheManagerTest.php
 * Holds: Tests the CacheManager
 * Created: 25.05.2015
 * Project: Youkok2
 * 
 */

use \Youkok2\Utilities\CacheManager as CacheManager;

class CacheManagerTest extends PHPUnit_Framework_TestCase {
    
    /*
     * Test isCached on non existing cache elements
     */
    
    public function testCacheStatus1() {
        // Get some cache results
        $cache1 = CacheManager::isCached(1, 'i');
        $cache2 = CacheManager::isCached(1000, 'i');
        $cache3 = CacheManager::isCached(null, 'i');
        $cache4 = CacheManager::isCached(null, null);
        
        // Assert that non of them exists
        $this->assertFalse($cache1);
        $this->assertFalse($cache2);
        $this->assertFalse($cache3);
        $this->assertFalse($cache4);
    }

    /*
     * Test isCache when creating invalid element
     */

    public function testCacheValidy() {
        // Cache one element with correct data
        $cache1 = CacheManager::setCache(1, 'i', ['foo' => 'bar'], true);

        // Cache one element with incorret data
        $cache2 = CacheManager::setCache(1, 'i', "foo", true);

        // Test validty
        $this->assertTrue($cache1);
        $this->assertFalse($cache2);
    }
    
    /*
     * Test isCached when creating one cached element
     */
    
    public function testCacheStatus2() {
        // Cache one element
        CacheManager::setCache(1, 'i', ['foo' => 'bar'], true);
        
        // Get cache result
        $cache1 = CacheManager::isCached(1, 'i');
        $cache2 = CacheManager::isCached(1, null);
        $cache3 = CacheManager::isCached(2, null);
        
        // Check if is set
        $this->assertTrue($cache1);
        
        // Check that ew cache does not affect other caches
        $this->assertFalse($cache2);
        $this->assertFalse($cache3);
    }
    
    /*
     * Test store method
     */
    
    public function testCacheStore() {
        // Cache one element
        CacheManager::setCache(2, 'i', ['foo' => 'bar']);
        
        // Get cache result without storing
        $cache1 = CacheManager::isCached(2, 'i');
        
        // Store all pending caches
        CacheManager::store();
        
        // Get cache result again
        $cache2 = CacheManager::isCached(2, 'i');
        
        // Check that ew cache does not affect other caches
        $this->assertFalse($cache1);
        $this->assertTrue($cache2);
    }
    
    /*
     * Test getCache
     */
    
    public function testCacheGet() {
        // Cache one element
        CacheManager::setCache(9999, 'i', ['foo' => 'bar'], true);
        
        // Get actual cache data
        $cache_data1 = CacheManager::getCache(9999, 'i');
        $cache_data2 = CacheManager::getCache(99999, 'i');
        
        // Cache that cache returned correctly
        $this->assertTrue(isset($cache_data1['foo']));
        $this->assertEquals('bar', $cache_data1['foo']);
        $this->assertNull($cache_data2);
    }
    
    /*
     * Test deleteCache
     */
    
    public function testCacheDelete() {
        // Cache and delete cache, then check status
        CacheManager::setCache(999999, 'i', ['foo' => 'bar'], true);
        CacheManager::deleteCache(999999, 'i');
        
        // Get status
        $cache_status = CacheManager::isCached(999999, 'i');
        
        // Chech that cache is indeed not present
        $this->assertFalse($cache_status);
    }

    // TODO test that setCache added last is the last to be inserted too
}