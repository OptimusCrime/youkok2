<?php
namespace Youkok\Web\Views;

use Slim\Http\Response;
use Slim\Http\Request;

class PageNotFound extends BaseView
{
    public function view(Request $request, Response $response): Response
    {
        return $this->render404($response);
    }
}
