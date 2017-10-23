<?php
namespace Youkok\Views\Admin;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use Youkok\Processors\Admin\File\AdminFileFetchProcessor;

class Files extends AdminBaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response)
    {
        $this->setSiteData('view', 'admin_files');

        return $this->render($response, 'admin/files.html', [
            'SITE_TITLE' => 'Admin',
            'ADMIN_TITLE' => 'Filer',
            'HEADER_MENU' => 'admin_files',
            'VIEW_NAME' => 'admin_files',
            'BODY_CLASS' => 'admin',
            'FILES' => AdminFileFetchProcessor::run()
        ]);
    }
}