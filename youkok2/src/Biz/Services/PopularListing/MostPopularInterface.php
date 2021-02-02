<?php
namespace Youkok\Biz\Services\PopularListing;

interface MostPopularInterface
{
    public function fromDelta(string $delta, int $limit): array;

    public function refreshAll(): void;

    public function refresh(string $delta): string;
}
