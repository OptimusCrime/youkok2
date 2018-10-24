<?php
namespace Youkok\Biz\Services\PopularListing;


interface MostPopularInterface {
    public function fromDelta($delta, $limit);

    public function refresh();
}