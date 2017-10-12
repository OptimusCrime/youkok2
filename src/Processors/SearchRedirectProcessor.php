<?php
namespace Youkok\Processors;

use \Psr\Http\Message\ResponseInterface as Response;

class SearchRedirectProcessor
{
    public static function run(Response $response, $router, $query)
    {
        $queryArr = static::splitQuery($query);
        $newSearchQuery = (count($queryArr) === 0) ? '' : static::generateNewSearchQuery($queryArr);

        return $response
            ->withStatus(302)
            ->withHeader('Location', $router->urlFor('search') . '?s=' . $newSearchQuery);
    }

    private static function splitQuery($query)
    {
        $splitRaw = explode(' ', $query);
        $splitClean = [];
        foreach ($splitRaw as $queryParameter) {
            if (strlen($queryParameter) > 0) {
                $splitClean[] = $queryParameter;
            }
        }

        return $splitClean;
    }

    private static function generateNewSearchQuery(array $queryArr)
    {
        $newQueryArr = [];
        foreach ($queryArr as $queryParameter) {
            $newQueryArr[] = $queryParameter . '*';
        }
        return implode('+', $newQueryArr);
    }

    private static function returnResponse(Response $response, $router, $searchQuery)
    {

    }
}
