<?php
namespace Youkok\Common\Middlewares;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

use Youkok\Biz\Services\SessionService;
use Youkok\Common\Utilities\CookieHelper;

class AdminAuthMiddleware
{
    private $container;

    /** @var \Youkok\Biz\Services\SessionService */
    private $sessionService;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->sessionService = $container->get(SessionService::class);
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $hash = CookieHelper::getCookie('youkok2');
        if ($hash === null or strlen($hash) === 0) {
            return static::noAccess($response);
        }

        $this->sessionService->init(false);

        $data = $this->sessionService->getSessionDataFromHash($hash);
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