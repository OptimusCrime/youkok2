<?php
namespace Youkok\Views\Admin;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Processors\LoginProcessor;
use Youkok\Views\BaseView;

class Home extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response)
    {
        return $this->render($response, 'admin/home.html', [
            'SITE_TITLE' => 'Admin',
            'HEADER_MENU' => 'admin_home',
            'VIEW_NAME' => 'admin_home'
        ]);
    }
}