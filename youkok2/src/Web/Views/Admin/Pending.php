<?php
namespace Youkok\Web\Views\Admin;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Admin\ElementListPendingProcessor;

class Pending extends AdminBaseView
{
    public function view(Request $request, Response $response)
    {
        $this->setSiteData('view', 'admin_pending');

        return $this->render($response, 'admin/pending.html', [
            'SITE_TITLE' => 'Admin',
            'ADMIN_TITLE' => 'Nye bidrag',
            'HEADER_MENU' => 'admin_pending',
            'VIEW_NAME' => 'admin_pending',
            'BODY_CLASS' => 'admin',
            'PENDING' => ElementListPendingProcessor::run()
        ]);
    }
}
