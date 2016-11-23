<?php
declare(strict_types=1);

namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class Flat extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function help(Request $request, Response $response, array $args): Response
    {
        return $this->render($response, 'help.tpl', [
            'SITE_TITLE' => 'Hjelp',
            'HEADER_MENU' => 'help',
            'VIEW_NAME' => 'help'
        ]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function about(Request $request, Response $response, array $args): Response
    {
        return $this->render($response, 'about.tpl', [
            'SITE_TITLE' => 'Om Youkok2',
            'HEADER_MENU' => 'about',
            'VIEW_NAME' => 'about'
        ]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function terms(Request $request, Response $response, array $args): Response
    {
        return $this->render($response, 'terms.tpl', [
            'SITE_TITLE' => 'Retningslinjer',
            'HEADER_MENU' => '',
            'VIEW_NAME' => 'terms'
        ]);
    }
}
