<?php
namespace Youkok\Web\Views\Admin;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Admin\ElementListProcessor;

class Files extends AdminBaseView
{
    public function view(Request $request, Response $response)
    {
        $this->setSiteData('view', 'admin_files');

        return $this->render($response, 'admin/files.html', [
            'SITE_TITLE' => 'Admin',
            'ADMIN_TITLE' => 'Filer',
            'HEADER_MENU' => 'admin_files',
            'VIEW_NAME' => 'admin_files',
            'BODY_CLASS' => 'admin',
            'FILES' => ElementListProcessor::run()
        ]);
    }
}
