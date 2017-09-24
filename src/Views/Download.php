<?php
namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Models\Element;

class Download extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        $element = Element::fromId($args['id']);
        if ($element === null) {
            return $this->render404($response);
        }

        $element->addDownload();

        return $this->render($response, 'flat/help.tpl', [
            'SITE_TITLE' => 'Hjelp',
            'HEADER_MENU' => 'help',
            'VIEW_NAME' => 'help'
        ]);
    }
}
