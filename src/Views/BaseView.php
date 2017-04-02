<?php
declare(strict_types=1);

namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\Container;

use Youkok\Helpers\SessionHandler;

class BaseView
{
    protected $container;
    protected $sessionHandler;
    protected $templateData;

    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->setDefaultTemplateData();
        $this->sessionHandler = new SessionHandler();
    }

    private function setDefaultTemplateData()
    {
        $this->templateData = [
            // Messages to display to the user
            'SITE_MESSAGES' => [],

            // Settings defined and/or overriden in our settings files
            'SITE_SETTINGS' => $this->container->get('settings')['site'],

            // Data to send to the site. Typically stuff we need for JavaScript things
            'SITE_DATA' => json_encode([]),

            // Information about the current user
            'USER' => [],

            // Other things
            'SITE_TITLE' => 'Den beste kokeboka på nettet',
            'HEADER_MENU' => 'home',
            'VIEW_NAME' => 'frontpage',
            'SEARCH_QUERY' => '',
        ];
    }

    protected function setTemplateData(string $key, $value)
    {
        $this->templateData[$key] = $value;
    }

    protected function render(Response $response, String $template, array $data): Response
    {
        $this->setTemplateData('USER', $this->sessionHandler->getData());

        $this->sessionHandler->store();

        return $this->container->get('view')->render($response, $template, array_merge(
            $this->templateData,
            $data
        ));
    }
}
