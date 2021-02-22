<?php
namespace Youkok\Biz\Services\Auth;

use Slim\Http\Request;

use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\InsufficientAccessException;
use Youkok\Biz\Exceptions\InvalidLoginAttemptException;
use Youkok\Helpers\Configuration\Configuration;

class AuthService
{
    const PARAM_PREFIX = 'password';

    const COOKIE_LIFETIME = 60 * 60 * 24 * 30; // 30 days

    /**
     * @param array $params
     * @throws InvalidLoginAttemptException
     */
    public function validateLogin(array $params): void
    {
        $configuration = Configuration::getInstance();

        for ($key = 1; $key <= 6; $key++) {
            if (!isset($params[static::PARAM_PREFIX . $key])) {
                throw new InvalidLoginAttemptException();
            }

            if (!password_verify($params[static::PARAM_PREFIX . $key], $configuration->getAdminPass($key))) {
                throw new InvalidLoginAttemptException();
            }
        }
    }

    /**
     * @throws InvalidLoginAttemptException
     */
    public function setAdminCookie(): void
    {
        $cookieValue = static::buildAdminCookieValue();

        $ret = setcookie(
            static::getCookieName(),
            $cookieValue,
            time() + static::COOKIE_LIFETIME,
            '/'
        );

        if ($ret === false) {
            // Failed to set cookie for some reason
            throw new InvalidLoginAttemptException();
        }
    }

    /**
     * @throws GenericYoukokException
     */
    public function removeAdminCookie(): void
    {
        $ret = setcookie(
            static::getCookieName(),
            null,
            time() - static::COOKIE_LIFETIME,
            '/'
        );

        if ($ret === false) {
            // Failed to set cookie for some reason
            throw new GenericYoukokException();
        }
    }

    /**
     * @param Request $request
     * @throws InsufficientAccessException
     */

    public function validateCookie(Request $request): void
    {
        $cookieValue = $request->getCookieParam(static::getCookieName());

        if ($cookieValue === null || mb_strlen($cookieValue) === 0) {
            throw new InsufficientAccessException();
        }

        if ($cookieValue !== static::buildAdminCookieValue()) {
            throw new InsufficientAccessException();
        }
    }

    public function isAdmin(Request $request): bool
    {
        try {
            $this->validateCookie($request);
            return true;
        }
        catch (InsufficientAccessException $e) {
            return false;
        }
    }

    private static function getCookieName(): string
    {
        return Configuration::getInstance()->getAdminCookie();
    }

    private static function buildAdminCookieValue(): string
    {
        $configuration = Configuration::getInstance();
        $value = '..-.';
        for ($key = 1; $key <= 6; $key++) {
            $value .= $configuration->getAdminPass($key) . '..;';
        }

        $value .= '--.-';

        return sha1($value);
    }
}
