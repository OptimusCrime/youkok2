<?php
namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Mappers\SearchMapper;
use Youkok\Processors\SearchProcessor;
use Youkok\Processors\SearchRedirectProcessor;

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
            $query = str_replace('|', '', strip_tags($request->getParams()[static::SEARCH_GET_PARAMETER]));
        }

        $searchResults = SearchProcessor::run($query);
        if (count($searchResults['results']) === 0) {
            return $this->redirectIfNoWildcardSearch($response, $query, $searchResults);
        }

        return $this->returnSearchResults($response, $query, $searchResults);
    }

    private function redirectIfNoWildcardSearch(Response $response, $query, array $searchResults)
    {
        if (strlen($query) > 0 and strpos($query, '*') === false) {
            return SearchRedirectProcessor::run($response, $this->container->get('router'), $query);
        }

        return $this->returnSearchResults($response, $query, $searchResults);
    }

    private function returnSearchResults(Response $response, $query, array $searchResults)
    {
        return $this->render($response, 'search.html', [
            'SITE_TITLE' => 'Søk',
            'HEADER_MENU' => 'search',
            'VIEW_NAME' => 'search',
            'RESULTS' => SearchMapper::map($searchResults['results'], $searchResults['permutations']),
            'SEARCH_QUERY' => $query
        ]);
    }
}
