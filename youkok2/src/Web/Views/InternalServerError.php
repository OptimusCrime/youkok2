<?php
namespace Youkok\Web\Views;

use Slim\Http\Response;
use Slim\Http\Request;

class InternalServerError extends BaseView
{
    public function view(Request $request, Response $response)
    {
        return $this->render404($response);
    }
}
