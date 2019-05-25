<?php
namespace Youkok\Web\Views;

use Slim\Http\Stream;
use Slim\Http\Response;
use Slim\Http\Request;
use Psr\Container\ContainerInterface;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Download\DownloadFileInfoService;
use Youkok\Biz\Services\Download\UpdateDownloadsService;
use Youkok\Common\Controllers\ElementController;

class Download extends BaseView
{
    /** @var DownloadFileInfoService */
    private $downloadService;

    /** @var UpdateDownloadsService */
    private $updateDownloadsProcessor;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->downloadService = $container->get(DownloadFileInfoService::class);
        $this->updateDownloadsProcessor = $container->get(UpdateDownloadsService::class);
    }

    public function view(Request $request, Response $response, array $args)
    {
        try {
            $element = ElementController::getNonDirectoryFromUri($args['uri']);

            if (!$this->downloadService->fileExists($element)) {
                // TODO logging

                throw new ElementNotFoundException();
            }

            $this->updateDownloadsProcessor->run($element);

            $fileInfo = $this->downloadService->getFileInfo($element);
            $fileSize = $this->downloadService->getFileSize($element);
            $fileContents = $this->downloadService->getFileContents($element);

            return $response
                ->withHeader('Content-Description', 'File Transfer')
                ->withHeader('Content-Type', $fileInfo)
                ->withHeader('Content-Disposition', 'inline; filename="' . $element->name . '"')
                ->withHeader('Expires', '0')
                ->withHeader('Cache-Control', 'must-revalidate')
                ->withHeader('Pragm', 'public')
                ->withHeader('Content-Length', $fileSize)
                ->withBody(new Stream($fileContents));
        }
        catch (ElementNotFoundException $e) {
            return $this->render404($response);
        }
    }
}
