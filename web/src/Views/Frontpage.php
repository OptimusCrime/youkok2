<?php
namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Processors\FrontpageFetchProcessor;

class Frontpage extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response)
    {
        return $this->render($response, 'frontpage.html', [
            'FRONTPAGE' => FrontpageFetchProcessor
                ::fromSessionHandler($this->sessionHandler)
                ->withCache($this->container->get('cache'))
                ->withSettings($this->container->get('settings'))
                ->run(),
            'BODY_CLASS' => 'frontpage'
        ]);
    }
}
