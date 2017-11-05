<?php
namespace Youkok\Views\Admin;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class Home extends AdminBaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response)
    {
        $this->setSiteData('view', 'admin_home');

        return $this->render($response, 'admin/home.html', [
            'SITE_TITLE' => 'Admin',
            'ADMIN_TITLE' => 'Hjem',
            'HEADER_MENU' => 'admin_home',
            'VIEW_NAME' => 'admin_home',
            'BODY_CLASS' => 'admin'
        ]);
    }
}
