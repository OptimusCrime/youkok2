<?php

namespace Youkok\Common\Controllers;

use Carbon\Carbon;

use Youkok\Biz\Exceptions\SessionNotFoundException;
use Youkok\Common\Models\Session;
use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;


class SessionController
{
    const SESSION_LIFE_TIME = 60 * 60 * 24 * 120; // 120 days

    const DEFAULT_DATA = [
        'most_popular_element' => MostPopularElement::MONTH,
        'most_popular_course' => MostPopularCourse::MONTH,
        'admin' => false
    ];

    public static function load(string $hash): Session
    {
        $session = Session::where('hash', $hash)->first();

        if ($session === null) {
            throw new SessionNotFoundException();
        }

        // Update session, set the default data, in case they have changed and the current user
        // is lacking some attributes
        $session->data = array_replace_recursive(json_decode($session->data, true), static::DEFAULT_DATA, true);

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
        $session->data = json_encode(static::DEFAULT_DATA);
        $session->last_updated = Carbon::now();
        $session->expire = Carbon::createFromTimestamp(time() + static::SESSION_LIFE_TIME);

        if ($session->save()) {
            throw new \Exception('Failed to create session');
        }

        return $session;
    }
}
