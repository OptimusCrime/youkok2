<?php
namespace Youkok\Web\Views;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\TemplateFileNotFoundException;

class PageNotFound extends BaseView
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws TemplateFileNotFoundException
     */
    public function view(Request $request, Response $response): Response
    {
        return $this->render404($response);
    }
}
