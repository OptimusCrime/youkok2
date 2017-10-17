<?php
namespace Youkok\Views\Admin;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class Files extends AdminBaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response)
    {
        $this->setSiteData('view', 'admin_files');

        return $this->render($response, 'admin/todo.html', [
            'SITE_TITLE' => 'Admin',
            'ADMIN_TITLE' => 'Filer',
            'HEADER_MENU' => 'admin_files',
            'VIEW_NAME' => 'admin_files',
            'BODY_CLASS' => 'admin'
        ]);
    }
}