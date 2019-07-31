<?php
namespace Youkok\Biz\Services\Admin;

use DateInterval;
use DateTime;
use Youkok\Biz\Services\Models\Admin\AdminDownloadService;

class HomeGraphService
{
    const RANGE_IN_DAYS = 30;

    private $adminDownloadService;

    public function __construct(AdminDownloadService $adminDownloadService)
    {
        $this->adminDownloadService = $adminDownloadService;
    }

    public function get(): array
    {
        $range = static::createRange();

        $downloads = $this->adminDownloadService->getGroupedDownloadsForRange(static::RANGE_IN_DAYS);

        foreach ($downloads as $download) {
            // This should always be true, but avoid messing up the array
            if (isset($range[$download->dtime])) {
                $range[$download->dtime] = $download->total;
            }
        }

        return $range;
    }

    private static function createRange(): array
    {
        $now = new DateTime();

        $range = [
            $now->format('Y-m-d') => 0
        ];

        for ($i = 1; $i <= static::RANGE_IN_DAYS; $i++) {
            $dayInPast = $now->sub(DateInterval::createFromDateString('1 day'));
            $range[$dayInPast->format('Y-m-d')] = 0;
        }

        return $range;
    }
}
