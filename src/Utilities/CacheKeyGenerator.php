<?php
namespace Youkok\Utilities;

class CacheKeyGenerator
{
    public static function keyForElementDownloads($id)
    {
        return 'downloads_' . $id;
    }
}