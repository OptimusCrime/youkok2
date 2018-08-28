<?php
namespace Youkok\Common\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;
use PHP_Timer;

class TimingMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        PHP_Timer::start();

        return $next($request, $response);
    }
}
