<?php
/*
 * File: CourseDownloadsTest.php
 * Holds: Tests the CourseDownloadsTest model
 * Created: 03.07.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Models;

use Youkok2\Models\Cache\CourseDownloads;

class CourseDownloadsTest extends \Youkok2\Tests\YoukokTestCase
{
    public static function tearDownAfterClass() {
        parent::doTearDownAfterClass();
    }

    public function testCourseDownloadsDefault() {
        $course_download = new CourseDownloads();

        $this->assertNull($course_download->getId());
        $this->assertNull($course_download->getData());
    }

    public function testCourseDownloadsCreateById() {
        $course_download = new CourseDownloads();
        $course_download->setId(10000);
        $course_download->setData('foo');
        $course_download->save();

        $course_download_fetched = new CourseDownloads($course_download->getId());
        $this->assertEquals(10000, $course_download_fetched->getId());
        $this->assertEquals('foo', $course_download_fetched->getData());
    }

    public function testCourseDownloadsCreateByArray() {
        $course_download = new CourseDownloads([
            'id' => 88,
            'data' => 'bar'
        ]);

        $this->assertEquals(88, $course_download->getId());
        $this->assertEquals('bar', $course_download->getData());
    }
}
