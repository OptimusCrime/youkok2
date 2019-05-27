<?php
namespace Youkok\Common\Controllers;

use Carbon\Carbon;

use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\SessionNotFoundException;
use Youkok\Common\Models\Session;

class SessionController
{
    const SESSION_LIFE_TIME = 60 * 60 * 24 * 120; // 120 days

    public static function get(string $hash): Session
    {
        $session = Session::get($hash);

        if ($session === null) {
            throw new SessionNotFoundException();
        }

        return $session;
    }

    public static function deleteExpiredSessions(): bool
    {
        return Session::select('id')
            ->where('expire', '<', Carbon::now())
            ->delete();
    }

    public static function create(string $hash): Session
    {
        $session = new Session();
        $session->hash = $hash;
        $session->last_updated = Carbon::now();
        $session->expire = Carbon::createFromTimestamp(time() + static::SESSION_LIFE_TIME);

        if (!$session->save()) {
            throw new GenericYoukokException('Failed to create session');
        }

        return $session;
    }
}
