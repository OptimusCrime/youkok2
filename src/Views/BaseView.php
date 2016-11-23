<?php
declare(strict_types=1);

namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\Container;

use Youkok\Helpers\SessionHandler;

class BaseView
{
    protected $container;
    protected $sessionData;
    protected $templateData;

    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->setDefaultTemplateData();

        $this->sessionData = SessionHandler::loadCurrentSession();
    }

    private function setDefaultTemplateData()
    {
        $this->templateData = [
            'SITE_TITLE' => 'Den beste kokeboka på nettet',
            'HEADER_MENU' => 'home',
            'VIEW_NAME' => 'frontpage',
            'SITE_DATA' => json_encode([]),
            'SEARCH_QUERY' => '',
            'SITE_MESSAGES' => []
        ];
    }

    protected function render(Response $response, String $template, array $data): Response
    {
        return $this->container->get('view')->render($response, $template, array_merge(
            $this->templateData,
            $data
        ));
    }
}
