<?php
namespace Youkok\Web\Views;

use Exception;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class PageNotFound extends BaseView
{
    /**
     * @throws Exception
     */
    public function view(Request $request, Response $response): Response
    {
        return $this->render404($response);
    }
}
