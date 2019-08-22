<?php
namespace Youkok\Common\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;
use SebastianBergmann\Timer\Timer;

class TimingMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        Timer::start();

        return $next($request, $response);
    }
}
