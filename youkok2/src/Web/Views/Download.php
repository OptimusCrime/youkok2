<?php

namespace Youkok\Web\Views;

use Slim\Http\Stream;
use Slim\Http\Response;
use Slim\Http\Request;
use Psr\Container\ContainerInterface;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Download\DownloadFileInfoService;
use Youkok\Biz\Services\Download\UpdateDownloadsService;
use Youkok\Biz\Services\Models\ElementService;

class Download extends BaseView
{
    /** @var DownloadFileInfoService */
    private $downloadService;

    /** @var UpdateDownloadsService */
    private $updateDownloadsProcessor;

    /** @var ElementService */
    private $elementService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->downloadService = $container->get(DownloadFileInfoService::class);
        $this->updateDownloadsProcessor = $container->get(UpdateDownloadsService::class);
        $this->elementService = $container->get(ElementService::class);
    }

    public function view(Request $request, Response $response, array $args)
    {
        try {
            // TODO this is not working
            $element = $this->elementService->getElementFromUri(
                $args['uri'],
                ['id', 'checksum', 'directory', 'name'],
                [
                    ElementService::FLAG_FETCH_PARENTS,
                    ElementService::FLAG_ENSURE_VISIBLE,
                    ElementService::FLAG_ENSURE_ALL_PARENTS_ARE_DIRECTORIES_CURRENT_IS_FILE
                ]
            );

            if (!$this->downloadService->fileExists($element)) {
                // TODO logging

                throw new ElementNotFoundException();
            }

            $this->updateDownloadsProcessor->run($element);

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
        } catch (ElementNotFoundException $e) {
            return $this->render404($response);
        } catch (\Exception $e) {
            return $this->render404($response);
        }
    }
}
