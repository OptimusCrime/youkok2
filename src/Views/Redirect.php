<?php
namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Models\Element;

class Redirect extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        $element = Element::fromId($args['id']);
        if ($element === null or $element->link === null) {
            return $this->render404($response);
        }

        $element->addDownload();

        return $response->withStatus(302)->withHeader('Location', $element->link);
    }
}
