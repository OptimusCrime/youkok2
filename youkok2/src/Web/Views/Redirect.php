<?php

namespace Youkok\Web\Views;

use Slim\Http\Response;
use Slim\Http\Request;
use Psr\Container\ContainerInterface;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Biz\Services\Download\UpdateDownloadsService;
use Youkok\Common\Utilities\SelectStatements;

class Redirect extends BaseView
{
    /** @var UpdateDownloadsService */
    private $updateDownloadsProcessor;

    /** @var ElementService */
    private $elementService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->updateDownloadsProcessor = $container->get(UpdateDownloadsService::class);
        $this->elementService = $container->get(ElementService::class);
    }

    public function view(Request $request, Response $response, array $args): Response
    {
        if (!isset($args['id']) || !is_numeric($args['id'])) {
            return $this->render404($response);
        }

        try {
            $element = $this->elementService->getElement(
                new SelectStatements('id', $args['id']),
                ['id', 'link'],
                [
                    ElementService::FLAG_ENSURE_VISIBLE
                ]
            );

            if ($element->link === null) {
                return $this->render404($response);
            }
        } catch (ElementNotFoundException $e) {
            return $this->render404($response);
        }

        $this->updateDownloadsProcessor->run($element);

        return $this->output(
            $response
                ->withStatus(302)
                ->withHeader('Location', $element->link)
        );
    }
}
