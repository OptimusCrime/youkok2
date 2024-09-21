<?php
namespace Youkok\Web\Views;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Noop
{
    public function view(Request $request, Response $response): Response
    {
        return $response->withStatus(500);
    }
}
