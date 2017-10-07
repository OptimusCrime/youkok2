<?php
namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use Youkok\Processors\SearchProcessor;

class Search extends BaseView
{
    const SEARCH_GET_PARAMETER = 's';

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        $query = null;
        if (isset($request->getParams()[static::SEARCH_GET_PARAMETER]) and strlen($request->getParams()[static::SEARCH_GET_PARAMETER]) > 0) {
            $query = $request->getParams()[static::SEARCH_GET_PARAMETER];
        }

        return $this->render($response, 'search.html', [
            'SITE_TITLE' => 'Søk',
            'HEADER_MENU' => 'search',
            'VIEW_NAME' => 'search',
            'RESULTS' => SearchProcessor::run($query),
            'SEARCH_QUERY' => $query
        ]);
    }
}
