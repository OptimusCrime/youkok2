<?php
namespace Youkok\Biz\Services\Models;

use Carbon\Carbon;

use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\SessionNotFoundException;
use Youkok\Common\Models\Session;

class SessionService
{
    const SESSION_LIFE_TIME = 60 * 60 * 24 * 120; // 120 days

    public static function get(string $hash): Session
    {
        $session = Session::where('hash', $hash)->first();

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

    public static function getOrCreate(string $hash): ?Session
    {
        /** @var Session $session */
        $session = Session::where('hash', $hash)->first();

        if ($session === null) {
            return null;
        }

        if ($session->data === null) {
            // Use the default values, which is set via the constructor
            return $session;
        }

        $data = json_decode($session->data, true);
        if (!is_array($data)) {
            return $session;
        }

        if (isset($data[Session::KEY_ADMIN]) and is_bool($data[Session::KEY_ADMIN])) {
            $session->setAdmin($data[Session::KEY_ADMIN]);
        }

        if (isset($data[Session::KEY_MOST_POPULAR_ELEMENT]) and is_String($data[Session::KEY_MOST_POPULAR_ELEMENT])) {
            $session->setMostPopularElement($data[Session::KEY_MOST_POPULAR_ELEMENT]);
        }

        if (isset($data[Session::KEY_MOST_POPULAR_COURSE]) and is_String($data[Session::KEY_MOST_POPULAR_COURSE])) {
            $session->setMostPopularCourse($data[Session::KEY_MOST_POPULAR_COURSE]);
        }

        return $session;
    }
}
