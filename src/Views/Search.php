<?php
namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class Search extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        return $this->render($response, 'search.tpl', [
            'SITE_TITLE' => 'Søk',
            'HEADER_MENU' => 'search',
            'VIEW_NAME' => 'search'
        ]);
    }
}
