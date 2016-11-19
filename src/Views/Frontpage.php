<?php
declare(strict_types=1);

namespace Youkok\Views;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Frontpage extends BaseView
{
    public function view(Request $request, Response $response, array $args)
    {
        return $this->ci->get('view')->render($response, 'frontpage.tpl', []);
    }
}
