<?php

namespace Youkok\Web\Views;

use Exception;
use Monolog\Logger;
use Slim\Http\Stream;
use Slim\Http\Response;
use Slim\Http\Request;
use Psr\Container\ContainerInterface;

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

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->downloadService = $container->get(DownloadFileInfoService::class);
        $this->updateDownloadsProcessor = $container->get(UpdateDownloadsService::class);
        $this->elementService = $container->get(ElementService::class);
        $this->authService = $container->get(AuthService::class);
        $this->logger = $container->get(Logger::class);
    }

    public function view(Request $request, Response $response, array $args)
    {
        $flags = [
            ElementService::FLAG_FETCH_PARENTS,
            ElementService::FLAG_ENSURE_ALL_PARENTS_ARE_DIRECTORIES_CURRENT_IS_FILE
        ];

        // If we are not currently logged in as admin, also make sure that the file is visible
        if (!$this->authService->isAdmin($request)) {
            $flags[] = ElementService::FLAG_ENSURE_VISIBLE;
        }

        try {
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

            return $this->output(
                $response
                    ->withHeader('Content-Description', 'File Transfer')
                    ->withHeader('Content-Type', $fileInfo)
                    ->withHeader('Content-Disposition', 'inline; filename="' . $element->name . '"')
                    ->withHeader('Expires', '0')
                    ->withHeader('Cache-Control', 'must-revalidate')
                    ->withHeader('Pragm', 'public')
                    ->withHeader('Content-Length', $fileSize)
                    ->withBody(new Stream(fopen($filePath, 'r')))
            );
        } catch (ElementNotFoundException | Exception $ex) {
            return $this->render404($response);
        }
    }
}
