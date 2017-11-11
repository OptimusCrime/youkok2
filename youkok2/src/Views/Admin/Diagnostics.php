<?php
namespace Youkok\Views\Admin;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class Diagnostics extends AdminBaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response)
    {
        $this->setSiteData('view', 'admin_diagnostics');

        return $this->render($response, 'admin/todo.html', [
            'SITE_TITLE' => 'Admin',
            'ADMIN_TITLE' => 'Diagnostikk',
            'HEADER_MENU' => 'admin_diagnostics',
            'VIEW_NAME' => 'admin_diagnostics',
            'BODY_CLASS' => 'admin'
        ]);
    }
}
