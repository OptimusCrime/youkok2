<?php
namespace Youkok\Controllers;

use Youkok\Models\Session;

use Carbon\Carbon;

class SessionController
{
    public static function getExpiredSessions()
    {
        return Session::select('id')
            ->where('expire', '<', Carbon::now())
            ->get();
    }
}