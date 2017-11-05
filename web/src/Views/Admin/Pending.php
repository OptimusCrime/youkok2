<?php
namespace Youkok\Views\Admin;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class Pending extends AdminBaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response)
    {
        $this->setSiteData('view', 'admin_pending');

        return $this->render($response, 'admin/todo.html', [
            'SITE_TITLE' => 'Admin',
            'ADMIN_TITLE' => 'Nye bidrag',
            'HEADER_MENU' => 'admin_pending',
            'VIEW_NAME' => 'admin_pending',
            'BODY_CLASS' => 'admin'
        ]);
    }
}
