<?php
namespace Youkok\Common\Utilities;

class CookieHelper
{
    // About a month
    const DEFAULT_COOKIE_LIFE_TIME = 60 * 60 * 24 * 30;

    public static function setCookie($name, $value, $lifeTime = self::DEFAULT_COOKIE_LIFE_TIME): bool
    {
        return setcookie($name, $value, time() + $lifeTime, '/');
    }

    public static function removeCookie($name): bool
    {
        return setcookie($name, null, time() - static::DEFAULT_COOKIE_LIFE_TIME, '/');
    }

    public static function getCookie($name): ?string
    {
        if (!isset($_COOKIE[$name]) or strlen($_COOKIE[$name]) === 0) {
            return null;
        }

        return $_COOKIE[$name];
    }
}
