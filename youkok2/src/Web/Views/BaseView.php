<?php
namespace Youkok\Web\Views;

use SebastianBergmann\Timer\Timer;
use Slim\Http\Response;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Views\Twig;
use Youkok\Biz\Services\UserSessionService;
use Youkok\Common\Utilities\CoursesCacheConstants;

class BaseView
{
    protected $container;

    /** @var Twig */
    protected $view;

    /** @var UserSessionService */
    protected $userSessionService;

    /** @var RouterInterface */
    protected $router;

    /** @var array */
    protected $templateData = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->view = $container->get('view');
        $this->userSessionService = $container->get(UserSessionService::class);
        $this->router = $container->get('router');

        $this->templateData = $this->getDefaultTemplateData();
    }

    private function getDefaultTemplateData()
    {
        $course_lookup = @file_get_contents(
            '/volume_data/cache/'
            . CoursesCacheConstants::DYNAMIC_SUB_DIRECTORY
            .  CoursesCacheConstants::CACHE_BUSTING_FILE_NAME
        );

        if ($course_lookup === false) {
            $course_lookup = '';
        }

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
                'courses_url' => $this->router->pathFor('courses')
            ],

            // Information about the current user
            'USER' => $this->userSessionService->getSession()->getAllData(),

            // Other things
            'SITE_TITLE' => 'Den beste kokeboka på nettet',
            'HEADER_MENU' => 'home',
            'VIEW_NAME' => 'frontpage',
            'SEARCH_QUERY' => '',

            'ADMIN' => $this->userSessionService->isAdmin(),
            'COURSE_LOOKUP' => $course_lookup
        ];
    }

    protected function setSiteData(string $key, $value)
    {
        $this->templateData['SITE_DATA'][$key] = $value;
    }

    protected function overrideTemplateData(string $key, $value)
    {
        $this->templateData[$key] = $value;
    }

    protected function output(Response $response): Response
    {
        $this->userSessionService->store();

        return $response;
    }

    protected function render(Response $response, string $template, array $data = []): Response
    {
        $this->userSessionService->store();

        return $this->view->render($response, $template, array_merge(
            $this->templateData,
            $data,
            [
                'EXECUTION_TIME' => Timer::secondsToTimeString(Timer::stop())
            ]
        ));
    }

    protected function renderReactApp(Response $response, string $template, array $data = []): Response
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

    protected function fetch(string $template, array $data = []): string
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
            'errors/404.html',
            [
                'HEADER_MENU' => '',
                'VIEW_NAME' => '404',
                'SITE_DESCRIPTION' => 'Siden ble ikke funnet.',
                'SITE_TITLE' => 'Siden ble ikke funnet',
            ]
        );
    }
}
