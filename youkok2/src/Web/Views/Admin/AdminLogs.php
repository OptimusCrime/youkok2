<?php
namespace Youkok\Web\Views\Admin;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Services\SystemLogService;

class AdminLogs extends AdminBaseView
{
    /** @var SystemLogService */
    private $systemLogService;

    /** @var Logger */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->systemLogService = $container->get(SystemLogService::class);
        $this->logger = $container->get(Logger::class);
    }

    public function view(Request $request, Response $response): Response
    {
        //$this->setSiteData('view', 'admin_logs');

        try {
            return $this->render($response, 'admin/logs.html', [
                'SITE_TITLE' => 'Admin',
                'ADMIN_TITLE' => 'Logger',
                'HEADER_MENU' => 'admin_logs',
                'VIEW_NAME' => 'admin_logs',
                'BODY_CLASS' => 'admin',
                'APP_LOG_CONTENT' => $this->systemLogService->fetch(),
            ]);
        } catch (GenericYoukokException $ex) {
            $this->logger->error($ex);

            return $response->withStatus(500);
        }
    }
}
