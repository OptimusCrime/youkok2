<?php
namespace Youkok\Web\Views\Admin;

use Slim\Http\Response;
use Slim\Http\Request;

class AdminHome extends AdminBaseView
{
    public function view(Request $request, Response $response): Response
    {
        //$this->setSiteData('view', 'admin_home');

        return $this->render($response, 'admin/home.html', [
            'SITE_TITLE' => 'Admin',
            'ADMIN_TITLE' => 'Hjem',
            'HEADER_MENU' => 'admin_home',
            'VIEW_NAME' => 'admin_home',
            'BODY_CLASS' => 'admin'
        ]);
    }
}
