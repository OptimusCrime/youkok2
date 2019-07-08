<?php
namespace Youkok\Biz\Services\Models;

use Carbon\Carbon;

use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\SessionNotFoundException;
use Youkok\Common\Models\Session;
use Youkok\Common\Utilities\CookieHelper;
use Youkok\Helpers\Utilities;

class SessionService
{
    const SESSION_TOKEN_LENGTH = 100;

    const SESSION_LIFE_TIME = 60 * 60 * 24 * 120; // 120 days

    public function get(string $hash): Session
    {
        $session = Session
            ::select('id', 'hash', 'data', 'last_updated', 'expire')
            ->where('hash', $hash)
            ->first();

        if ($session === null) {
            throw new SessionNotFoundException();
        }

        return $session;
    }

    public function deleteExpiredSessions(): bool
    {
        return Session::select('id')
            ->where('expire', '<', Carbon::now())
            ->delete();
    }

    public function create(): Session
    {
        $hash = Utilities::randomToken(self::SESSION_TOKEN_LENGTH);

        CookieHelper::setCookie('youkok2', $hash, SessionService::SESSION_LIFE_TIME);

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
