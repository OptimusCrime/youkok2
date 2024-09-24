<?php
namespace Youkok\Biz\Services\Admin;

use DateInterval;
use DateTime;

use Youkok\Biz\Services\Models\Admin\AdminDownloadService;

class HomeGraphService
{
    const int RANGE_IN_DAYS = 30;

    private AdminDownloadService $adminDownloadService;

    public function __construct(AdminDownloadService $adminDownloadService)
    {
        $this->adminDownloadService = $adminDownloadService;
    }

    public function get(): array
    {
        $range = static::createRange();

        $output = [];
        foreach ($range as $date) {
            $output[] = [
                'date' => $date,
                'value' => $this->adminDownloadService->getDownloadsOnDate($date),
            ];
        }

        return $output;
    }

    private static function createRange(): array
    {
        $now = new DateTime();

        $range = [
            $now->format('Y-m-d'),
        ];

        for ($i = 1; $i <= static::RANGE_IN_DAYS; $i++) {
            $dayInPast = $now->sub(DateInterval::createFromDateString('1 day'));
            $range[] = $dayInPast->format('Y-m-d');
        }

        return $range;
    }
}
