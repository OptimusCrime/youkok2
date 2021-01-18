<?php
namespace Youkok\Web\Views\Admin;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;

use Youkok\Biz\Services\Models\ElementService;
use Youkok\Web\Views\BaseView;

class AdminBaseView extends BaseView
{
    // TODO: Place into rest endpoint
    /*
    protected function render(Response $response, string $template, array $data = []): Response
    {
        return parent::render($response, $template, array_merge(
            $data,
            [
                'NUM_PENDING' => $this->elementService->getAllPending()
            ]
        ));
    }
    */
}
