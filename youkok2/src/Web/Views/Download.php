<?php
namespace Youkok\Web\Views;

use Exception;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Psr\Container\ContainerInterface;

use Slim\Psr7\Stream;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Auth\AuthService;
use Youkok\Biz\Services\Download\DownloadFileInfoService;
use Youkok\Biz\Services\Download\UpdateDownloadsService;
use Youkok\Biz\Services\Models\ElementService;

class Download extends BaseView
{
    private DownloadFileInfoService $downloadService;
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
        $this->downloadService = $container->get(DownloadFileInfoService::class);
        $this->updateDownloadsProcessor = $container->get(UpdateDownloadsService::class);
        $this->elementService = $container->get(ElementService::class);
        $this->authService = $container->get(AuthService::class);
        $this->logger = $container->get('logger');
    }

    public function view(Request $request, Response $response, array $args): Response
    {
        try {
            $flags = [
                ElementService::FLAG_FETCH_PARENTS,
                ElementService::FLAG_ENSURE_ALL_PARENTS_ARE_DIRECTORIES_CURRENT_IS_FILE
            ];

            // If we are not currently logged in as admin, also make sure that the file is visible
            if (!$this->authService->isAdmin($request)) {
                $flags[] = ElementService::FLAG_ENSURE_VISIBLE;
            }

            $element = $this->elementService->getElementFromUri(
                $args['uri'],
                ['id', 'checksum', 'directory', 'name'],
                $flags
            );

            if (!$this->downloadService->fileExists($element)) {
                $this->logger->error('Tried to download file which does not exist. Id: ' . $element->id);

                throw new ElementNotFoundException();
            }

            if (!$this->authService->isAdmin($request)) {
                $this->updateDownloadsProcessor->run($element);
            }

            $fileInfo = $this->downloadService->getFileInfo($element);
            $fileSize = $this->downloadService->getFileSize($element);
            $filePath = $this->downloadService->getFilePath($element);

            return $response
                ->withHeader('Content-Description', 'File Transfer')
                ->withHeader('Content-Type', $fileInfo)
                ->withHeader('Content-Disposition', 'inline; filename="' . $element->name . '"')
                ->withHeader('Expires', '0')
                ->withHeader('Cache-Control', 'must-revalidate')
                ->withHeader('Pragm', 'public')
                ->withHeader('Content-Length', $fileSize)
                ->withBody(new Stream(fopen($filePath, 'r')));
        } catch (ElementNotFoundException $ex) {
            $this->logger->debug($ex);

            try {
                return $this->render404($response);
            }
            catch (Exception $ex) {
                $this->logger->error($ex);
                return $response->withStatus(500);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex);
            return $response->withStatus(500);
        }
    }
}
