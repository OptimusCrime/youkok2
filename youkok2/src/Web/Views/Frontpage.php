<?php
namespace Youkok\Web\Views;

use Slim\Http\Response;
use Slim\Http\Request;
use Psr\Container\ContainerInterface;

class Frontpage extends BaseView
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    public function view(Request $request, Response $response)
    {
        return $this->renderReactApp($response, 'frontpage.html', [
            'BODY_CLASS' => 'frontpage'
        ]);
    }
}
