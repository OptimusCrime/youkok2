<?php
namespace Youkok\Utilities;

class UriTranslator
{
    public static function generate($fileName)
    {
        // Replace first here to keep "norwegian" names in a way
        $fileName = str_replace(['Æ', 'Ø', 'Å'], ['ae', 'o', 'aa'], $fileName);
        $fileName = str_replace(['æ', 'ø', 'å'], ['ae', 'o', 'aa'], $fileName);

        // Replace multiple spaces to dashes and remove special chars
        $fileName = preg_replace('!\s+!', '-', $fileName);
        return preg_replace('![^-_a-z0-9\s\.]+!', '', strtolower($fileName));
    }
}