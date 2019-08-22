<?php
namespace Youkok\Biz\Services\Job\Jobs;

use Youkok\Biz\Services\Models\SessionService;

class RemoveOldSessionsJobServiceJobService implements JobServiceInterface
{
    private $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }


    public function run()
    {
        $this->sessionService->deleteExpiredSessions();
    }
}
