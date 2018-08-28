<?php
namespace Youkok\Biz\Services;

use Slim\Http\Response;
use Slim\Interfaces\RouterInterface;

class SearchRedirectService
{
    private $response;
    private $router;

    public function __construct(Response $response, RouterInterface $router)
    {
        $this->response = $response;
        $this->router = $router;
    }

    public function run($query)
    {
        $queryArr = static::splitQuery($query);
        $newSearchQuery = (count($queryArr) === 0) ? '' : static::generateNewSearchQuery($queryArr);

        return $this->response
            ->withStatus(302)
            ->withHeader('Location', $this->router->pathFor('search') . '?s=' . $newSearchQuery);
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
}
