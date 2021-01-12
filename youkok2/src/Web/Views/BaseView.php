<?php
namespace Youkok\Web\Views;

use Slim\Http\Response;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Views\Twig;
use Youkok\Common\Utilities\CoursesCacheConstants;

class BaseView
{
    protected ContainerInterface $container;

    protected Twig $view;
    protected RouterInterface $router;
    protected array $templateData = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->view = $container->get('view');
        $this->router = $container->get('router');

        $this->templateData = [
            'SITE_DATA' => []
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
        return $response;
    }

    protected function render(Response $response, string $template, array $data = []): Response
    {
        return $this->view->render($response, $template, array_merge(
            $this->templateData,
            $data,
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

    // TODO Remove
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
