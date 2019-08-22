<?php
namespace Youkok\Web\Views\Admin;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;

use Youkok\Biz\Services\Models\ElementService;
use Youkok\Web\Views\BaseView;

class AdminBaseView extends BaseView
{
    /** @var ElementService */
    private $elementService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->elementService = $container->get(ElementService::class);
    }

    protected function render(Response $response, string $template, array $data = []): Response
    {
        return parent::render($response, $template, array_merge(
            $data,
            [
                'NUM_PENDING' => $this->elementService->getAllPending()
            ]
        ));
    }

    protected function renderReactApp(Response $response, string $template, array $data = []): Response
    {
        return parent::renderReactApp($response, $template, array_merge(
            $data,
            [
                'NUM_PENDING' => $this->elementService->getAllPending()
            ]
        ));
    }
}
