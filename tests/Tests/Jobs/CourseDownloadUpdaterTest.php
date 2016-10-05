<?php
namespace Youkok2\Tests\Views;

use Youkok2\Models\Download;
use Youkok2\Models\Element;
use Youkok2\Models\Cache\CourseDownloads;
use Youkok2\Jobs\CourseDownloadUpdater;
use Youkok2\Utilities\CacheManager;

class CourseDownloadUpdaterTest extends \Youkok2\Tests\YoukokTestCase
{
    public static function tearDownAfterClass() {
        parent::doTearDownAfterClass();
    }

    public function testCourseDownloadUpdater() {
        $individual_downloads = [
            1, 5, 4, 22, 15, 1, 2, 7, 3, 23, 11, 5, 44, 7, 8, 15, 3, 6, 2, 1, 7, 10, 12, 4, 121
        ];

        for ($i = 1; $i <= 25; $i++) {
            $course = new Element();
            $course->setParent(null);
            $course->setName('Foo' . $i . '||Course ' . $i);
            $course->setPending(false);
            $course->setDeleted(false);
            $course->save();

            for ($j = 0; $j < 2; $j++) {
                $element = new Element();
                $element->setName('Element ' . $j);
                $element->setParent($course->getId());
                $element->setPending(false);
                $element->save();

                for ($k = 0; $k < $individual_downloads[$i - 1]; $k++) {
                    $download = new Download();
                    $download->setFile($element->getId());

                    if ($i <= 3) {
                        // Today, current time minus 4 hours
                        $download->setDownloadedTime(date('Y-m-d H:i:s', (time() - (60 * 60 * 4))));
                    }
                    elseif ($i <= 6) {
                        // This week, current time minus 5 days
                        $download->setDownloadedTime(date('Y-m-d H:i:s', (time() - (60 * 60 * 24 * 5))));
                    }
                    elseif ($i <= 10) {
                        // This month, current time minus 23 days
                        $download->setDownloadedTime(date('Y-m-d H:i:s', (time() - (60 * 60 * 24 * 23))));
                    }
                    elseif ($i <= 13) {
                        // This year, current time minus 122 days
                        $download->setDownloadedTime(date('Y-m-d H:i:s', (time() - (60 * 60 * 24 * 122))));
                    }
                    else {
                        // Always, current time minus 720 days
                        $download->setDownloadedTime(date('Y-m-d H:i:s', (time() - (60 * 60 * 24 * 720))));
                    }

                    $download->save();
                }
            }
        }

        $invalid_element = new Element();
        $invalid_element->setName('Element invalid');
        $invalid_element->setParent(99999999);
        $invalid_element->setPending(false);
        $invalid_element->save();

        $invalid_download = new Download();
        $invalid_download->setFile($invalid_element->getId());
        $invalid_download->save();

        $courseDownloadUpdater = new CourseDownloadUpdater();
        $courseDownloadUpdater->run();
        $courseDownloadUpdater->done();
        
        $course_downloads_always = new CourseDownloads(0);
        $course_downloads_always_data = json_decode($course_downloads_always->getData(), true);
        $this->assertEquals(15, count($course_downloads_always_data));
        $this->assertEquals(242, $course_downloads_always_data[0]['downloaded']);

        $course_downloads_today = new CourseDownloads(1);
        $course_downloads_today_data = json_decode($course_downloads_today->getData(), true);
        $this->assertEquals(3, count($course_downloads_today_data));
        $this->assertEquals(10, $course_downloads_today_data[0]['downloaded']);

        $course_downloads_week = new CourseDownloads(2);
        $course_downloads_week_data = json_decode($course_downloads_week->getData(), true);
        $this->assertEquals(6, count($course_downloads_week_data));
        $this->assertEquals(44, $course_downloads_week_data[0]['downloaded']);

        $course_downloads_month = new CourseDownloads(3);
        $course_downloads_month_data = json_decode($course_downloads_month->getData(), true);
        $this->assertEquals(10, count($course_downloads_month_data));
        $this->assertEquals(46, $course_downloads_month_data[0]['downloaded']);

        $course_downloads_year = new CourseDownloads(4);
        $course_downloads_year_data = json_decode($course_downloads_year->getData(), true);
        $this->assertEquals(13, count($course_downloads_year_data));
        $this->assertEquals(88, $course_downloads_year_data[0]['downloaded']);
    }
}
