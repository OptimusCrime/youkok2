<?php
namespace Youkok\Web\Views\Admin;

use Slim\Http\Response;
use Slim\Http\Request;

class Scripts extends AdminBaseView
{
    public function view(Request $request, Response $response)
    {
        $this->setSiteData('view', 'admin_scripts');

        return $this->render($response, 'admin/todo.html', [
            'SITE_TITLE' => 'Admin',
            'ADMIN_TITLE' => 'Scripts',
            'HEADER_MENU' => 'admin_scripts',
            'VIEW_NAME' => 'admin_scripts',
            'BODY_CLASS' => 'admin'
        ]);
    }
}
