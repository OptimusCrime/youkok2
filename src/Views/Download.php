<?php
namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Helpers\DownloadHelper;
use Youkok\Helpers\ElementHelper;
use Youkok\Models\Element;

class Download extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        $element = Element::fromUri($args['params'], true);
        if ($element === null) {
            return $this->render404($response);
        }

        $element->addDownload();

        $downloadResponse = DownloadHelper::render($response, $element, $this->container->get('settings')->all());
        if ($downloadResponse === null) {
            return $this->render404($response);
        }

        return $downloadResponse;
    }
}
