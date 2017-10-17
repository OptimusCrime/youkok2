<?php
namespace Youkok\Views\Admin;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class Logs extends AdminBaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response)
    {
        $this->setSiteData('view', 'admin_logs');

        return $this->render($response, 'admin/todo.html', [
            'SITE_TITLE' => 'Admin',
            'ADMIN_TITLE' => 'Logger',
            'HEADER_MENU' => 'admin_logs',
            'VIEW_NAME' => 'admin_logs',
            'BODY_CLASS' => 'admin'
        ]);
    }
}