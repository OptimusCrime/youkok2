<?php
namespace Youkok\Views\Admin;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Processors\LoginProcessor;
use Youkok\Views\BaseView;

class Login extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function display(Request $request, Response $response)
    {
        return $this->render($response, 'admin/login.html', [
            'SITE_TITLE' => 'Admin',
            'HEADER_MENU' => 'admin',
            'VIEW_NAME' => 'admin'
        ]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function submit(Request $request, Response $response)
    {
        return LoginProcessor::fromParams($request->getParams())
            ->withSessionHandler($this->sessionHandler)
            ->withResponse($response)
            ->withPasswords($this->container->get('settings')['admin'])
            ->withRouter($this->container->get('router'))
            ->run();
    }
}