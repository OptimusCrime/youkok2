<?php
namespace Youkok\Web\Views\Admin;

use Slim\Http\Response;
use Slim\Http\Request;

class AdminStatistics extends AdminBaseView
{
    public function view(Request $request, Response $response): Response
    {
        // $this->setSiteData('view', 'admin_statistics');

        return $this->render($response, 'admin/todo.html', [
            'SITE_TITLE' => 'Admin',
            'ADMIN_TITLE' => 'Statistikk',
            'HEADER_MENU' => 'admin_statistics',
            'VIEW_NAME' => 'admin_statistics',
            'BODY_CLASS' => 'admin'
        ]);
    }
}
