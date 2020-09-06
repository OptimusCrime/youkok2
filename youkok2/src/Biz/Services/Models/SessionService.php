<?php
namespace Youkok\Biz\Services\Models;

use Carbon\Carbon;

use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\SessionNotFoundException;
use Youkok\Common\Models\Session;
use Youkok\Common\Utilities\CookieHelper;

class SessionService
{
    const SESSION_TOKEN_LENGTH = 100;
    const SESSION_LIFE_TIME = 60 * 60 * 24 * 7; // seven days or one week, who needs these sessions after all?
    const KEYSPACE = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public function get(string $hash): Session
    {
        $session = Session
            ::select('id', 'hash', 'data', 'expire')
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
        $hash = static::createRandomToken(self::SESSION_TOKEN_LENGTH);

        CookieHelper::setCookie('youkok2', $hash, SessionService::SESSION_LIFE_TIME);

        $session = new Session();
        $session->hash = $hash;
        $session->expire = Carbon::createFromTimestamp(time() + static::SESSION_LIFE_TIME);

        if (!$session->save()) {
            throw new GenericYoukokException('Failed to create session');
        }

        return $session;
    }

    private function createRandomToken(int $length): string
    {
        $str = '';
        $max = mb_strlen(static::KEYSPACE, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= static::KEYSPACE[random_int(0, $max)];
        }
        return $str;
    }
}
