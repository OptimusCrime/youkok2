<?php
namespace Youkok\Common\Controllers;

use Carbon\Carbon;

use Youkok\Common\Models\Session;


class SessionController
{
    public static function deleteExpiredSessions()
    {
        return Session::select('id')
            ->where('expire', '<', Carbon::now())
            ->delete();
    }
}