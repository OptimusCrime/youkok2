<?php
namespace Youkok\Utilities;

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
}
