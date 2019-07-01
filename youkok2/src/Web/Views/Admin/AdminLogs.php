<?php
namespace Youkok\Web\Views\Admin;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Services\SystemLogService;

class AdminLogs extends AdminBaseView
{
    /** @var SystemLogService */
    private $systemLogService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->systemLogService = $container->get(SystemLogService::class);
    }

    public function view(Request $request, Response $response): Response
    {
        $this->setSiteData('view', 'admin_logs');

        return $this->render($response, 'admin/logs.html', [
            'SITE_TITLE' => 'Admin',
            'ADMIN_TITLE' => 'Logger',
            'HEADER_MENU' => 'admin_logs',
            'VIEW_NAME' => 'admin_logs',
            'BODY_CLASS' => 'admin',
            'PHP_LOG_CONTENT' => $this->systemLogService->fetch(SystemLogService::PHP_LOG),
            'ERROR_LOG_CONTENT' => $this->systemLogService->fetch(SystemLogService::ERROR_LOG),
        ]);
    }
}
