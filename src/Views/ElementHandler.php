<?php
declare(strict_types=1);

namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Models\Download;
use Youkok\Models\Element;

class ElementHandler extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function redirect(Request $request, Response $response, array $args): Response
    {
        $element = $this->getRequestedElement($args);
        if ($element === null or $element->link === null) {
            return $this->render404($response);
        }

        $this->addDownload($element);

        return $response->withStatus(302)->withHeader('Location', $element->link);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function download(Request $request, Response $response, array $args): Response
    {
        $element = $this->getRequestedElement($args);
        if ($element === null) {
            return $this->render404($response);
        }

        $this->addDownload($element);

        return $this->render($response, 'flat/help.tpl', [
            'SITE_TITLE' => 'Hjelp',
            'HEADER_MENU' => 'help',
            'VIEW_NAME' => 'help'
        ]);
    }

    private function getRequestedElement(array $args)
    {
        if (!isset($args['id']) or !is_numeric($args['id'])) {
            return null;
        }

        return Element::select('id', 'link', 'checksum')
            ->where('id', $args['id'])
            ->where('deleted', 0)
            ->where('pending', 0)
            ->first();
    }

    private function addDownload(Element $element)
    {
        $download = new Download();
        $download->resource = $element->id;
        $download->ip = $_SERVER['REMOTE_ADDR']; // TODO
        $download->agent = $_SERVER['HTTP_USER_AGENT']; // TODO
        $download->save();
    }
}
