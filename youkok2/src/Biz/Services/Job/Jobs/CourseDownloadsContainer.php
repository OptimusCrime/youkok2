<?php

namespace Youkok\Biz\Services\Job\Jobs;

use Youkok\Common\Models\Element;

class CourseDownloadsContainer
{
    private int $downloadsToday = 0;
    private int $downloadsWeek = 0;
    private int $downloadsMonth = 0;
    private int $downloadsYear = 0;

    private readonly int $courseId;

    public function __construct(
        int $courseId,
    )
    {
        $this->courseId = $courseId;
    }

    function run(): void
    {
        $this->summarizeDownloads($this->courseId);
    }

    private function summarizeDownloads(int $id): void
    {
        $children = Element::where('parent', $id)->get();

        /** @var Element $child */
        foreach ($children as $child) {
            if ($child->isDirectory()) {
                $this->summarizeDownloads($child->id);
                continue;
            }

            $this->downloadsToday += $child->downloads_today;
            $this->downloadsWeek += $child->downloads_week;
            $this->downloadsMonth += $child->downloads_month;
            $this->downloadsYear += $child->downloads_year;
        }
    }

    public function getDownloadsToday(): int
    {
        return $this->downloadsToday;
    }

    public function getDownloadsWeek(): int
    {
        return $this->downloadsWeek;
    }

    public function getDownloadsMonth(): int
    {
        return $this->downloadsMonth;
    }

    public function getDownloadsYear(): int
    {
        return $this->downloadsYear;
    }
}
