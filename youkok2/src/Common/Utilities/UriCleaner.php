<?php
namespace Youkok\Common\Utilities;

class UriCleaner
{
    public static function clean($uri)
    {
        $fragments = [];
        $uriSplit = explode('/', $uri);
        foreach ($uriSplit as $item) {
            if (strlen($item) > 0) {
                $fragments[] = $item;
            }
        }

        return implode('/', $fragments);
    }

    public static function cleanFragments(array $fragments)
    {
        $clean = [];
        foreach ($fragments as $fragment) {
            if ($fragment !== null and strlen($fragment) > 0) {
                $clean[] = $fragment;
            }
        }

        return $clean;
    }

    public static function cleanAll($uri)
    {
        return preg_replace("/[^A-Za-z]/", '', $uri);
    }
}
