<?php
/*
 * File: FavoriteTest.php
 * Holds: Tests the Favorite model
 * Created: 14.06.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Models;

use Youkok2\Models\Favorite;

class FavoriteTest extends \Youkok2\Tests\YoukokTestCase
{
    public static function tearDownAfterClass() {
        parent::doTearDownAfterClass();
    }

    public function testFavoriteDefault() {
        // Create favorite
        $favorite = new Favorite();

        // Assert
        $this->assertNull($favorite->getId());
        $this->assertNull($favorite->getFile());
        $this->assertNull($favorite->getUser());
        $this->assertEquals('CURRENT_TIMESTAMP', $favorite->getFavoritedTime());
    }

    public function testFavoriteave() {
        // Create new favorite
        $favorite = new Favorite();

        // Set some fields we need
        $favorite->setFile(1);
        $favorite->setUser(1);

        // Save favorite
        $favorite->save();

        // Check that favorite was saved
        $this->assertTrue(is_numeric($favorite->getId()));
    }

    public function testFavoriteGettersSetters() {
        $favorite = new Favorite();
        $favorite->setId(1);
        $favorite->setFile(2);
        $favorite->setUser(3);
        $favorite->setFavoritedTime('2011-11-11 12:12:12');

        // Check that getters and setters down correctly
        $this->assertEquals(1, $favorite->getId());
        $this->assertEquals(2, $favorite->getFile());
        $this->assertEquals(3, $favorite->getUser());
        $this->assertEquals('2011-11-11 12:12:12', $favorite->getFavoritedTime());
    }

    public function testFavoriteCreateBy() {
        // By array
        $favorite1 = new Favorite([
            'id' => 999
        ]);
        $this->assertEquals(999, $favorite1->getId());

        // By id
        $favorite2 = new Favorite();
        $favorite2->setFile(1);
        $favorite2->setUser(2);
        $favorite2->save();

        // Fetch the saved instance
        $favorite_fetched = new Favorite($favorite2->getId());
        $this->assertEquals($favorite2->getId(), $favorite_fetched->getId());
        $this->assertEquals(1, $favorite_fetched->getFile());
        $this->assertEquals(2, $favorite_fetched->getUser());
    }
}
