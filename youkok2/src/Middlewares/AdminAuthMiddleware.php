<?php
namespace Youkok\Middlewares;

use Youkok\Helpers\SessionHandler;
use Youkok\Utilities\CookieHelper;

class AdminAuthMiddleware
{
    public function __invoke($request, $response, $next)
    {
        $hash = CookieHelper::getCookie('youkok2');
        if ($hash === null or strlen($hash) === 0) {
            return static::noAccess($response);
        }

        $sessionHandler = new SessionHandler(false);
        $data = $sessionHandler->getSessionDataFromHash($hash);
        if (!isset($data['admin']) or !$data['admin']) {
            return static::noAccess($response);
        }

        return $next($request, $response);
    }

    private static function noAccess($response)
    {
        return $response->withStatus(403);
    }
}