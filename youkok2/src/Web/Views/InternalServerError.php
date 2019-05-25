<?php
namespace Youkok\Web\Views;

use Slim\Http\Response;
use Slim\Http\Request;

// TODO, do not extend BaseView?
class InternalServerError extends BaseView
{
    public function view(Request $request, Response $response): Response
    {
        return $this->render404($response);
    }
}
