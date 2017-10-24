<?php
namespace Youkok\Views;

use PHP_Timer;
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
        $this->sessionHandler = new SessionHandler();

        $this->container = $container;
        $this->templateData = $this->getDefaultTemplateData();
    }

    private function getDefaultTemplateData()
    {
        return [
            // Messages to display to the user
            'SITE_MESSAGES' => [],

            // Settings defined and/or overriden in our settings files
            'SITE_SETTINGS' => $this->container->get('settings')['site'],

            // Data to send to the site. Typically stuff we need for JavaScript things
            'SITE_DATA' => [
                'version' => $this->container->get('settings')['site']['VERSION']
            ],

            // Information about the current user
            'USER' => $this->sessionHandler->getData(),

            // Other things
            'SITE_TITLE' => 'Den beste kokeboka på nettet',
            'HEADER_MENU' => 'home',
            'VIEW_NAME' => 'frontpage',
            'SEARCH_QUERY' => '',
        ];
    }

    protected function setSiteData($key, $value)
    {
        $this->templateData['SITE_DATA'][$key] = $value;
    }

    private function cleanUp()
    {
        $stored = $this->sessionHandler->store();
        if (!$stored) {
            // Make sure we update the last_updated field in the database
            $this->sessionHandler->update();
        }
    }

    protected function returnResponse(Response $response)
    {
        $this->cleanUp();

        return $response;
    }

    protected function render(Response $response, $template, array $data = [])
    {
        $this->cleanUp();
        $this->templateData['ADMIN'] = $this->sessionHandler->isAdmin();
        $this->templateData['EXECUTION_TIME'] = PHP_Timer::secondsToTimeString(PHP_Timer::stop());

        return $this->container->get('view')->render($response, $template, array_merge(
            $this->templateData,
            $data
        ));
    }

    protected function render404(Response $response)
    {
        return $this->render($response, 'errors/404.html', [
            'HEADER_MENU' => '',
            'VIEW_NAME' => '404',
        ]);
    }
}
