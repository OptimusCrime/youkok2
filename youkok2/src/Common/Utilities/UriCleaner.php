<?php
namespace Youkok\Common\Utilities;

class UriCleaner
{
    public static function cleanFragments(array $fragments): array
    {
        $clean = [];
        foreach ($fragments as $fragment) {
            if ($fragment !== null and mb_strlen($fragment) > 0) {
                $clean[] = preg_replace("/[^A-Za-z0-9-_.]/", '', $fragment);
            }
        }

        return $clean;
    }

    public static function cleanUri(string $uri): string
    {
        return implode('/', static::cleanFragments(explode('/', $uri)));
    }
}
