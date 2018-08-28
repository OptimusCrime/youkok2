<?php
namespace Youkok\Web\Views\Admin;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\LoginProcessor;
use Youkok\Web\Views\BaseView;

class Login extends BaseView
{
    public function display(Request $request, Response $response)
    {
        return $this->render($response, 'admin/login.html', [
            'SITE_TITLE' => 'Admin',
            'HEADER_MENU' => 'admin',
            'VIEW_NAME' => 'admin'
        ]);
    }

    public function submit(Request $request, Response $response)
    {
        return LoginProcessor::fromParams($request->getParams())
            ->withSessionHandler($this->sessionService)
            ->withResponse($response)
            ->withPasswords($this->container->get('settings')['admin'])
            ->withRouter($this->container->get('router'))
            ->run();
    }
}
