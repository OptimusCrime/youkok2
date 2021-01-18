<?php
namespace Youkok\Web\Views\Admin;

use Slim\Http\Response;
use Slim\Http\Request;

class AdminPending extends AdminBaseView
{
    public function view(Request $request, Response $response): Response
    {
        //$this->setSiteData('view', 'admin_pending');

        return $this->render($response, 'admin/pending.html', [
            'SITE_TITLE' => 'Admin',
            'ADMIN_TITLE' => 'Ventende',
            'HEADER_MENU' => 'admin_pending',
            'VIEW_NAME' => 'admin_pending',
            'BODY_CLASS' => 'admin'
        ]);
    }
}
