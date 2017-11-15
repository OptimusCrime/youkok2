<?php
namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Helpers\DownloadHelper;
use Youkok\Models\Element;
use Youkok\Processors\UpdateDownloadsProcessor;

class Download extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        $element = Element::fromUri($args['params'], Element::ELEMENT_TYPE_FILE_LAST);
        if ($element === null) {
            return $this->render404($response);
        }

        $downloadResponse = DownloadHelper::render($response, $element, $this->container->get('settings')->all());
        if ($downloadResponse === null) {
            return $this->render404($response);
        }

        UpdateDownloadsProcessor
            ::fromElement($element)
            ->withSessionHandler($this->sessionHandler)
            ->withCache($this->container->get('cache'))
            ->run();

        return $this->returnResponse($downloadResponse);
    }
}
