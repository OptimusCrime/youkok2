<?php
namespace Youkok\Web\Views;

use PHP_Timer;
use Slim\Http\Response;
use Psr\Container\ContainerInterface;
use Youkok\Biz\Services\SessionService;

class BaseView
{
    protected $container;

    /** @var \Slim\Views\Twig */
    protected $view;

    /** @var \Youkok\Biz\Services\SessionService */
    protected $sessionService;

    protected $templateData;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->view = $container->get('view');
        $this->sessionService = $container->get(SessionService::class);

        $this->sessionService->init();

        $this->templateData = $this->getDefaultTemplateData();
    }

    private function getDefaultTemplateData()
    {
        return [
            'DEV' => getenv('DEV'),

            'SITE_SETTINGS' => [
                'VERSION' => getenv('SITE_VERSION'),
                'EMAIL_CONTACT' => getenv('SITE_EMAIL_CONTACT'),

                'GOOGLE_ANALYTICS' => getenv('SITE_GOOGLE_ANALYTICS') === '1',
                'GOOGLE_SENSE' => getenv('SITE_GOOGLE_SENSE') === '1',
                'GOOGLE_ANALYTICS_CODE' => getenv('SITE_GOOGLE_ANALYTICS_CODE'),
                'GOOGLE_SENSE_CODE' => getenv('SITE_GOOGLE_SENSE_CODE'),

            ],

            'SITE_DATA' => [
            ],

            // Information about the current user
            'USER' => $this->sessionService->getAllData(),

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
        $this->templateData['ADMIN'] = $this->sessionService->isAdmin();
        $this->templateData['EXECUTION_TIME'] = PHP_Timer::secondsToTimeString(PHP_Timer::stop());

        $stored = $this->sessionService->store();
        if (!$stored) {
            // Make sure we update the last_updated field in the database
            $this->sessionService->update();
        }
    }

    protected function returnResponse(Response $response): Response
    {
        $this->cleanUp();

        return $response;
    }

    protected function render(Response $response, $template, array $data = []): Response
    {
        $this->cleanUp();

        return $this->view->render($response, $template, array_merge(
            $this->templateData,
            $data
        ));
    }

    protected function renderReactApp(Response $response, $template, array $data = []): Response
    {
        $reactBaseDir = 'react' . DIRECTORY_SEPARATOR;

        if (getenv('DEV') === '0') {
            return $this->render(
                $response,
                $reactBaseDir . $template,
                $data
            );
        }

        return $this->render(
            $response,
            $reactBaseDir . 'dev_' . $template,
            $data
        );
    }

    protected function fetch($template, array $data = []): string
    {
        return $this->view->fetch(
            $template,
            array_merge(
                $this->templateData,
                $data
            )
        );
    }

    protected function render404(Response $response): Response
    {
        return $this->render(
            $response->withStatus(404),
            'errors/404.html', [
                'HEADER_MENU' => '',
                'VIEW_NAME' => '404',
                'SITE_DESCRIPTION' => 'Siden ble ikke funnet.',
                'SITE_TITLE' => 'Siden ble ikke funnet'
            ]
        );
    }
}
