<?php
/*
 * File: ViewCoursesTest.php
 * Holds: Tests the Frontpage view
 * Created: 25.05.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Views;

use Youkok2\Youkok2;
use Youkok2\Models\Element;

class ViewCoursesTest extends \Youkok2\Tests\YoukokTestCase
{
    protected static $doTeardown = true;

    public function testViewCourses() {
        // Add two courses
        $element1 = new Element();
        $element1->setName('Foo');
        $element1->setUrlFriendly('foo');
        $element1->setParent(null);
        $element1->setDirectory(true);
        $element1->setPending(false);
        $element1->save();

        $element2 = new Element();
        $element2->setName('Bar');
        $element2->setUrlFriendly('bar');
        $element2->setParent(null);
        $element2->setDirectory(true);
        $element2->setPending(false);
        $element2->save();

        // Create view for courses
        $courses_wrapper = new Youkok2();
        $courses_wrapper->load('emner', [
            'close_db' => false
        ]);
        $this->assertEquals(200, $courses_wrapper->getStatus());
    }
}
