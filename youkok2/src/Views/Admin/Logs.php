<?php
namespace Youkok\Views\Admin;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use Youkok\Processors\Admin\SystemLogProcessor;

class Logs extends AdminBaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response)
    {
        $this->setSiteData('view', 'admin_logs');

        return $this->render($response, 'admin/logs.html', [
            'SITE_TITLE' => 'Admin',
            'ADMIN_TITLE' => 'Logger',
            'HEADER_MENU' => 'admin_logs',
            'VIEW_NAME' => 'admin_logs',
            'BODY_CLASS' => 'admin',
            'PHP_LOG_CONTENT' => SystemLogProcessor::fetch(SystemLogProcessor::PHP_LOG),
            'ERROR_LOG_CONTENT' => SystemLogProcessor::fetch(SystemLogProcessor::ERROR_LOG),
        ]);
    }
}
