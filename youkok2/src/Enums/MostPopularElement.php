<?php
namespace Youkok\Enums;

abstract class MostPopularElement
{
    const DAY = 'DAY';
    const WEEK = 'WEEK';
    const MONTH = 'MONTH';
    const YEAR = 'YEAR';
    const ALL = 'ALL';

    public static function all()
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
