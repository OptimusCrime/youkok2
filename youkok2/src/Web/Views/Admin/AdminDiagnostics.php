<?php
namespace Youkok\Web\Views\Admin;

use Slim\Http\Response;
use Slim\Http\Request;

class AdminDiagnostics extends AdminBaseView
{
    public function view(Request $request, Response $response): Response
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
