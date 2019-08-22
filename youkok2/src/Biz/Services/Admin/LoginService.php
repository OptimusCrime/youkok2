<?php
namespace Youkok\Biz\Services\Admin;

use Youkok\Biz\Exceptions\InvalidLoginAttemptException;

class LoginService
{
    const PARAM_PREFIX = 'password';
    const ENV_PREFIX = 'ADMIN_PASS';

    public function validateLogin(array $params): void
    {
        for ($key = 1; $key <= 6; $key++) {
            if (!isset($params[static::PARAM_PREFIX . $key])) {
                throw new InvalidLoginAttemptException();
            }

            if (!password_verify($params[static::PARAM_PREFIX . $key], getenv(static::ENV_PREFIX . $key))) {
                throw new InvalidLoginAttemptException();
            }
        }
    }
}
