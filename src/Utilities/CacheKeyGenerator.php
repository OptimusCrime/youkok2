<?php
namespace Youkok\Utilities;

class CacheKeyGenerator
{
    public static function keyForElementDownloads($id)
    {
        return 'downloads_' . $id;
    }

    public static function keyForMostPopularElementsForDelta($delta)
    {
        return 'most_popular_delta_' . $delta;
    }
}