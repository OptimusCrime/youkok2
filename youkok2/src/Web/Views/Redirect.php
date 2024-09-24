<?php
namespace Youkok\Web\Views;

use Exception;

use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Psr\Container\ContainerInterface;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Auth\AuthService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Biz\Services\Download\UpdateDownloadsService;
use Youkok\Common\Utilities\SelectStatements;

class Redirect extends BaseView
{
    private UpdateDownloadsService $updateDownloadsProcessor;
    private ElementService $elementService;
    private AuthService $authService;
    private Logger $logger;

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->updateDownloadsProcessor = $container->get(UpdateDownloadsService::class);
        $this->elementService = $container->get(ElementService::class);
        $this->authService = $container->get(AuthService::class);
        $this->logger = $container->get('logger');
    }

    public function view(Request $request, Response $response, array $args): Response
    {
        try {
            if (!isset($args['id']) || !is_numeric($args['id'])) {
                return $this->render404($response);
            }

            $flags = [
                ElementService::FLAG_FETCH_COURSE
            ];

            // If we are not currently logged in as admin, also make sure that the file is visible
            if (!$this->authService->isAdmin($request)) {
                $flags[] = ElementService::FLAG_ENSURE_VISIBLE;
            }


            $element = $this->elementService->getElement(
                new SelectStatements('id', $args['id']),
                $flags
            );

            if ($element->link === null) {
                return $this->render404($response);
            }

            if (!$this->authService->isAdmin($request)) {
                try {
                    $this->updateDownloadsProcessor->run($element);
                }
                catch (Exception $ex) {
                    $this->logger->error($ex);
                }
            }

            return $response
                ->withStatus(302)
                ->withHeader('Location', $element->link);
        } catch (ElementNotFoundException $ex) {
            $this->logger->error('Non existing element with id: ' . $args['id'] . ' attempted redirected.');

            try {
                return $this->render404($response);
            }
            catch (Exception $ex) {
                $this->logger->error($ex);
                return $response
                    ->withStatus(500);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex);
            return $response
                ->withStatus(500);
        }
    }
}
