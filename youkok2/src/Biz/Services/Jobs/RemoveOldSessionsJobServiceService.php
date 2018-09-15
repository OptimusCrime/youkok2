<?php
namespace Youkok\Biz\Services\Jobs;

use Youkok\Biz\Services\SessionService;

class RemoveOldSessionsJobServiceService implements JobServiceInterface
{
    /** @var \Youkok\Biz\Services\SessionService */
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
