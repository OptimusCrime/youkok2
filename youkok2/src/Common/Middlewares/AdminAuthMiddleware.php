<?php
namespace Youkok\Common\Middlewares;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

use Youkok\Biz\Exceptions\CookieNotFoundException;
use Youkok\Biz\Exceptions\SessionNotFoundException;
use Youkok\Biz\Services\SessionService;
use Youkok\Common\Controllers\SessionController;
use Youkok\Common\Utilities\CookieHelper;

class AdminAuthMiddleware
{
    /** @var SessionService */
    private $sessionService;

    public function __construct(ContainerInterface $container)
    {
        $this->sessionService = $container->get(SessionService::class);
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        try {
            $hash = CookieHelper::getCookie('youkok2');
            $session = SessionController::get($hash);

            if ($session->isAdmin()) {
                return $next($request, $response);
            }
        }
        catch (SessionNotFoundException $e) {
            return static::noAccess($response);
        } catch (CookieNotFoundException $e) {
            return static::noAccess($response);
        }

        return static::noAccess($response);
    }

    private static function noAccess(Response $response)
    {
        return $response->withStatus(403);
    }
}
