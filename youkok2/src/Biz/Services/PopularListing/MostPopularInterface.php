<?php
namespace Youkok\Biz\Services\PopularListing;


interface MostPopularInterface {
    public function fromDelta(string $delta, int $limit);

    public function refresh();
}
