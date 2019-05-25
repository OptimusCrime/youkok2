<?php
namespace Youkok\Web\Views;

use Slim\Http\Response;
use Slim\Http\Request;
use Psr\Container\ContainerInterface;

use Youkok\Common\Models\Element;
use Youkok\Biz\Services\Download\UpdateDownloadsService;

class Redirect extends BaseView
{
    /** @var \Youkok\Biz\Services\Download\UpdateDownloadsService */
    private $updateDownloadsProcessor;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->updateDownloadsProcessor = $container->get(UpdateDownloadsService::class);
    }

    public function view(Request $request, Response $response, array $args)
    {
        $element = Element::fromIdVisible($args['id']);
        if ($element === null or $element->link === null) {
            return $this->render404($response);
        }

        $this->updateDownloadsProcessor->run($element);

        return $response->withStatus(302)
            ->withHeader('Location', $element->link);
    }
}
