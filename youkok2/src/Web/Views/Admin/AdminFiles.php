<?php
namespace Youkok\Web\Views\Admin;

use Slim\Http\Response;
use Slim\Http\Request;

class AdminFiles extends AdminBaseView
{
    public function view(Request $request, Response $response): Response
    {
        $this->setSiteData('view', 'admin_files');

        return $this->render($response, 'admin/files.html', [
            'SITE_TITLE' => 'Admin',
            'ADMIN_TITLE' => 'Filer',
            'HEADER_MENU' => 'admin_files',
            'VIEW_NAME' => 'admin_files',
            'BODY_CLASS' => 'admin'
        ]);
    }
}
