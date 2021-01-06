<?php
namespace Youkok\Biz\Services\Admin;

use Youkok\Biz\Exceptions\InvalidLoginAttemptException;
use Youkok\Helpers\Configuration\Configuration;

class LoginService
{
    const PARAM_PREFIX = 'password';

    public function validateLogin(array $params): void
    {
        for ($key = 1; $key <= 6; $key++) {
            if (!isset($params[static::PARAM_PREFIX . $key])) {
                throw new InvalidLoginAttemptException();
            }

            $configuration = Configuration::getInstance();

            if (!password_verify($params[static::PARAM_PREFIX . $key], $configuration->getAdminPass($key))) {
                throw new InvalidLoginAttemptException();
            }
        }
    }
}
