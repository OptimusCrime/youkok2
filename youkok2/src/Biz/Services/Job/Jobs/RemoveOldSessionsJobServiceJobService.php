<?php
namespace Youkok\Biz\Services\Job\Jobs;

use Youkok\Biz\Services\Models\SessionService;
use Youkok\Biz\Services\UserSessionService;

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
