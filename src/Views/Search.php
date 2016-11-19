<?php
declare(strict_types=1);

namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class Search extends BaseView
{
    public function view(Request $request, Response $response, array $args): Response
    {
        return $this->render($response, 'search.tpl', []);
    }
}
