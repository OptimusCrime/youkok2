<?php
namespace Youkok\Web\Views;

use Slim\Http\Response;
use Slim\Http\Request;
use Psr\Container\ContainerInterface;

use Slim\Http\Stream;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Download\DownloadFileInfoService;
use Youkok\Biz\Services\Element\ElementService;

class Download extends BaseView
{
    /** @var \Youkok\Biz\Services\Download\DownloadFileInfoService */
    private $downloadService;

    /** @var \Youkok\Biz\Services\Element\ElementService */
    private $elementService;


    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->downloadService = $container->get(DownloadFileInfoService::class);
        $this->elementService = $container->get(ElementService::class);
    }

    public function view(Request $request, Response $response, array $args)
    {
        try {
            $element = $this->elementService->getNonDirectoryFromUri($args['uri']);

            if (!$this->downloadService->fileExists($element)) {
                // TODO logging

                throw new ElementNotFoundException();
            }

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
