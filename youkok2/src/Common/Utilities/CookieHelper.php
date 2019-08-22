<?php
namespace Youkok\Common\Utilities;

use Youkok\Biz\Exceptions\CookieNotFoundException;

class CookieHelper
{
    // About a month
    const DEFAULT_COOKIE_LIFE_TIME = 60 * 60 * 24 * 30;

    public static function setCookie($name, $value, $lifeTime = self::DEFAULT_COOKIE_LIFE_TIME): bool
    {
        return setcookie($name, $value, time() + $lifeTime, '/');
    }

    public static function getCookie($key): string
    {
        if (!isset($_COOKIE[$key]) or mb_strlen($_COOKIE[$key]) === 0) {
            throw new CookieNotFoundException();
        }

        return $_COOKIE[$key];
    }
}
