<?php
namespace Youkok\Enums;

use MyCLabs\Enum\Enum;

use Youkok\Biz\Exceptions\InvalidValueException;

class MostPopularCourse extends Enum
{
    private const string DAY = 'DAY';
    private const string WEEK = 'WEEK';
    private const string MONTH = 'MONTH';
    private const string YEAR = 'YEAR';
    private const string ALL = 'ALL';

    public function eq(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        return $this->value === $value;
    }

    /**
     * @throws InvalidValueException
     */
    public static function fromValue(?string $value): MostPopularCourse
    {
        if ($value !== null && !self::isValid($value)) {
            throw new InvalidValueException($value . ' is not a valid value for ' . self::class);
        }

        return new MostPopularCourse($value);
    }

    public static function DAY(): MostPopularCourse
    {
        return new MostPopularCourse(static::DAY);
    }

    public static function WEEK(): MostPopularCourse
    {
        return new MostPopularCourse(static::WEEK);
    }

    public static function MONTH(): MostPopularCourse
    {
        return new MostPopularCourse(static::MONTH);
    }

    public static function YEAR(): MostPopularCourse
    {
        return new MostPopularCourse(static::YEAR);
    }

    public static function ALL(): MostPopularCourse
    {
        return new MostPopularCourse(static::ALL);
    }

    public static function collection(): array
    {
        return [
            static::DAY(),
            static::WEEK(),
            static::MONTH(),
            static::YEAR(),
            static::ALL(),
        ];
    }
}
