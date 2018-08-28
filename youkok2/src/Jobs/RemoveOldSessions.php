<?php
namespace Youkok\Jobs;

use Youkok\Common\Controllers\SessionController;

class RemoveOldSessions extends JobInterface
{
    public function run()
    {
        $sessions = SessionController::getExpiredSessions();
        foreach ($sessions as $session) {
            $session->delete();
        }
    }
}
