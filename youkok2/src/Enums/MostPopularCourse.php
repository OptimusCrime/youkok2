<?php
namespace Youkok\Enums;

abstract class MostPopularCourse
{
    const DAY = 'DAY';
    const WEEK = 'WEEK';
    const MONTH = 'MONTH';
    const YEAR = 'YEAR';
    const ALL = 'ALL';

    public static function all(): array
    {
        return [
            static::DAY,
            static::WEEK,
            static::MONTH,
            static::YEAR,
            static::ALL,
        ];
    }
}
