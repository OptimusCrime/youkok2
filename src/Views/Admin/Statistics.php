<?php
namespace Youkok\Views\Admin;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class Statistics extends AdminBaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response)
    {
        $this->setSiteData('view', 'admin_statistics');

        return $this->render($response, 'admin/todo.html', [
            'SITE_TITLE' => 'Admin',
            'ADMIN_TITLE' => 'Statistikk',
            'HEADER_MENU' => 'admin_statistics',
            'VIEW_NAME' => 'admin_statistics',
            'BODY_CLASS' => 'admin'
        ]);
    }
}