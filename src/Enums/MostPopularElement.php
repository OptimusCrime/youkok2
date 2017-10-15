<?php
namespace Youkok\Enums;

abstract class MostPopularElement
{
    const TODAY = 0;
    const WEEK = 1;
    const MONTH = 2;
    const YEAR = 3;
    const ALL = 4;

    public static function all()
    {
        return [
            static::TODAY,
            static::WEEK,
            static::MONTH,
            static::YEAR,
            static::ALL,
        ];
    }
}
