<?php
namespace Youkok\Enums;

abstract class MostPopularCourse
{
    const TODAY = 'TODAY';
    const WEEK = 'WEEK';
    const MONTH = 'MONTH';
    const YEAR = 'YEAR';
    const ALL = 'ALL';

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
