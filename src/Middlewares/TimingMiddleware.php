<?php
namespace Youkok\Middlewares;

class TimingMiddleware
{
    public function __invoke($request, $response, $next)
    {
        \PHP_Timer::start();

        return $next($request, $response);
    }
}