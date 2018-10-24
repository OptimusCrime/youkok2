<?php
namespace Youkok\Web\Views;

use Slim\Http\Request;
use Slim\Http\Response;
use Psr\Container\ContainerInterface;

use Youkok\Mappers\SearchMapper;
use Youkok\Biz\SearchProcessor;
use Youkok\Biz\Services\SearchRedirectService;

class Search extends BaseView
{
    const SEARCH_GET_PARAMETER = 's';

    /** @var \Slim\Interfaces\RouterInterface */
    private $router;

    /** @var \Youkok\Biz\Services\SearchRedirectService */
    private $searchRedirectProcessor;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->router = $container->get('router');
        $this->searchRedirectProcessor = $container->get(SearchRedirectService::class);
    }

    public function view(Request $request, Response $response)
    {

        $query = null;
        if (isset($request->getParams()[static::SEARCH_GET_PARAMETER])
            and strlen($request->getParams()[static::SEARCH_GET_PARAMETER]) > 0) {
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
            $newSearchQuery = $this->searchRedirectProcessor->run($query);

            return $response
                ->withStatus(302)
                ->withHeader('Location', $this->router->pathFor('search') . '?s=' . $newSearchQuery);
        }

        return $this->returnSearchResults($response, $query, $searchResults);
    }

    private function returnSearchResults(Response $response, $query, array $searchResults)
    {
        return $this->render(
            $response,
                'search.html', [
                'SITE_TITLE' => 'Søk',
                'HEADER_MENU' => 'search',
                'VIEW_NAME' => 'search',
                'RESULTS' => SearchMapper::map($searchResults['results'], $searchResults['permutations']),
                'SEARCH_QUERY' => $query,
                'SITE_DESCRIPTION' => 'Søk etter emner på NTNU'
            ]
        );
    }
}
