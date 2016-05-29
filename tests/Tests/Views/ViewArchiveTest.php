<?php
/*
 * File: ViewArchiveTest.php
 * Holds: Tests the Archive view
 * Created: 30.05.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Views;

use Youkok2\Youkok2;
use Youkok2\Models\Element;

class ViewArchiveTest extends \Youkok2\Tests\YoukokTestCase
{
    protected static $doTeardown = true;

    public function testViewArchive() {
        // Course
        $element1 = new Element();
        $element1->setName('AAA1000||Test');
        $element1->setUrlFriendly('aaa1000');
        $element1->setParent(null);
        $element1->setEmpty(false);
        $element1->setDirectory(true);
        $element1->setPending(false);
        $element1->setDeleted(false);
        $element1->save();

        // Folder
        $element2 = new Element();
        $element2->setName('Folder');
        $element2->setUrlFriendly('folder');
        $element2->setParent($element1->getId());
        $element2->setEmpty(false);
        $element2->setDirectory(true);
        $element2->setPending(false);
        $element2->setDeleted(false);
        $element2->save();

        // Link in folder
        $element3 = new Element();
        $element3->setName('Google');
        $element3->setParent($element2->getId());
        $element3->setEmpty(false);
        $element3->setDirectory(false);
        $element3->setPending(false);
        $element3->setDeleted(false);
        $element3->setUrl('http://www.google.com');
        $element3->save();

        // Test view for course
        $archive_wrapper = new Youkok2();
        $archive_wrapper->load('emner/aaa1000', [
            'close_db' => false
        ]);

        // Assert that the view was loaded correctly
        $this->assertEquals(200, $archive_wrapper->getStatus());

        // Test view for folder in course
        $archive_wrapper->load('emner/aaa1000/folder', [
            'close_db' => false
        ]);

        // Assert that the view was loaded correctly
        $this->assertEquals(200, $archive_wrapper->getStatus());
    }
}
