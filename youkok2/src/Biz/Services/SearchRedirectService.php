<?php
namespace Youkok\Biz\Services;

// NOT used anymore?
// TODO
class SearchRedirectService
{
    public function run($query)
    {
        $queryArr = static::splitQuery($query);
        return (count($queryArr) === 0) ? '' : static::generateNewSearchQuery($queryArr);
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
