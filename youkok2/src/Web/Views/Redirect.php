<?php
namespace Youkok\Web\Views;

use Exception;

use Slim\Http\Response;
use Slim\Http\Request;
use Psr\Container\ContainerInterface;

use Youkok\Biz\Exceptions\TemplateFileNotFoundException;
use Youkok\Biz\Services\Auth\AuthService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Biz\Services\Download\UpdateDownloadsService;
use Youkok\Common\Utilities\SelectStatements;

class Redirect extends BaseView
{
    private UpdateDownloadsService $updateDownloadsProcessor;
    private ElementService $elementService;
    private AuthService $authService;

    public function __construct(ContainerInterface $container)
    {
        $this->updateDownloadsProcessor = $container->get(UpdateDownloadsService::class);
        $this->elementService = $container->get(ElementService::class);
        $this->authService = $container->get(AuthService::class);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws TemplateFileNotFoundException
     */
    public function view(Request $request, Response $response, array $args): Response
    {
        if (!isset($args['id']) || !is_numeric($args['id'])) {
            return $this->render404($response);
        }

        $flags = [];

        // If we are not currently logged in as admin, also make sure that the file is visible
        if (!$this->authService->isAdmin($request)) {
            $flags[] = ElementService::FLAG_ENSURE_VISIBLE;
        }

        try {
            $element = $this->elementService->getElement(
                new SelectStatements('id', $args['id']),
                ['id', 'link', 'parent'],
                $flags
            );

            if ($element->link === null) {
                return $this->render404($response);
            }
        } catch (Exception $e) {
            return $this->render404($response);
        }

        if (!$this->authService->isAdmin($request)) {
            $this->updateDownloadsProcessor->run($element);
        }

        return $response
            ->withStatus(302)
            ->withHeader('Location', $element->link);
    }
}
