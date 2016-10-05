<?php
namespace Youkok2\Tests\Models;

use Youkok2\Models\Favorite;

class FavoriteTest extends \Youkok2\Tests\YoukokTestCase
{
    public static function tearDownAfterClass() {
        parent::doTearDownAfterClass();
    }

    public function testFavoriteDefault() {
        $favorite = new Favorite();

        $this->assertNull($favorite->getId());
        $this->assertNull($favorite->getFile());
        $this->assertNull($favorite->getUser());
        $this->assertEquals('CURRENT_TIMESTAMP', $favorite->getFavoritedTime());
    }

    public function testFavoriteave() {
        $favorite = new Favorite();

        $favorite->setFile(1);
        $favorite->setUser(1);

        $favorite->save();

        $this->assertTrue(is_numeric($favorite->getId()));
    }

    public function testFavoriteGettersSetters() {
        $favorite = new Favorite();
        $favorite->setId(1);
        $favorite->setFile(2);
        $favorite->setUser(3);
        $favorite->setFavoritedTime('2011-11-11 12:12:12');

        $this->assertEquals(1, $favorite->getId());
        $this->assertEquals(2, $favorite->getFile());
        $this->assertEquals(3, $favorite->getUser());
        $this->assertEquals('2011-11-11 12:12:12', $favorite->getFavoritedTime());
    }

    public function testFavoriteCreateBy() {
        $favorite1 = new Favorite([
            'id' => 999
        ]);
        $this->assertEquals(999, $favorite1->getId());
        
        $favorite2 = new Favorite();
        $favorite2->setFile(1);
        $favorite2->setUser(2);
        $favorite2->save();
        
        $favorite_fetched = new Favorite($favorite2->getId());
        $this->assertEquals($favorite2->getId(), $favorite_fetched->getId());
        $this->assertEquals(1, $favorite_fetched->getFile());
        $this->assertEquals(2, $favorite_fetched->getUser());
    }
}
