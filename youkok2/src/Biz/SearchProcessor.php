<?php
namespace Youkok\Biz;

use Youkok\Common\Controllers\ElementController;

class SearchProcessor
{
    public static function run($query = null)
    {
        $permutations = static::getPermutationsFromSearch($query);

        return [
            'results' => ElementController::getElementsFromSearch($permutations),
            'permutations' => $permutations
        ];
    }

    private static function getPermutationsFromSearch($search)
    {
        $splitSearch = static::splitSearch($search);

        if (count($splitSearch) > 5) {
            return [];
        }

        $sqlSearch = static::translateToSQL($splitSearch);
        return static::calculatePermutations($sqlSearch);
    }

    private static function splitSearch($search)
    {
        if (strpos($search, ' ') === false) {
            return [$search];
        }

        $searchSplit = explode(' ', $search);
        $searchClean = [];
        foreach ($searchSplit as $v) {
            if (strlen($v) > 0) {
                $searchClean[] = $v;
            }
        }

        return $searchClean;
    }

    private static function translateToSQL(array $search)
    {
        $newSearch = [];
        foreach ($search as $v) {
            $newSearch[] = str_replace('*', '%', $v);
        }

        return $newSearch;
    }

    private static function calculatePermutations(array $search)
    {
        if (count($search) === 1) {
            return [
                $search[0] . '||%',
                '%||' . $search[0]
            ];
        }

        $query = [];
        $permutations = static::pcPermute($search);
        foreach ($permutations as $v) {
            // Partition the array. First result should have no values on the left side, and all on the right,
            // second should have one values on the left side, and the remaining right, ...
            $partitions = static::partitionPermutations($v);
            foreach ($partitions as $par) {
                $left = '%';
                if (count($par['left']) > 0) {
                    $left = implode('', $par['left']);
                }

                $right = '%';
                if (count($par['right']) > 0) {
                    $right = implode('', $par['right']);
                }

                $query[] = $left . '||' . $right;
            }
        }

        return $query;
    }

    private static function partitionPermutations(array $values)
    {
        $partitions = [];
        for ($i = 0; $i < (count($values) + 1); $i++) {
            $partitions[] = static::partitionArray($values, $i);
        }

        return $partitions;
    }

    private static function partitionArray(array $values, $leftSide)
    {
        // TODO write description here
        $left = [];
        $right = [];
        for ($i = 0; $i < $leftSide; $i++) {
            $left[] = $values[$i];
        }
        for ($i = $leftSide; $i < count($values); $i++) {
            $right[] = $values[$i];
        }

        return [
            'left' => $left,
            'right' => $right
        ];
    }

    // TODO split out and clean up
    private static function pcPermute($items, $perms = [ ])
    {
        if (empty($items)) {
            return [$perms];
        }

        $return = [];
        for ($i = count($items) - 1; $i >= 0; --$i) {
            $newitems = $items;
            $newperms = $perms;
            list($foo) = array_splice($newitems, $i, 1);
            array_unshift($newperms, $foo);
            $return = array_merge($return, static::pcPermute($newitems, $newperms));
        }

        return $return;
    }
}
