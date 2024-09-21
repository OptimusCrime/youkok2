<?php
namespace Youkok\Enums;

use MyCLabs\Enum\Enum;

use Youkok\Biz\Exceptions\InvalidValueException;

class MostPopularElement extends Enum
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
    public static function fromValue(?string $value): MostPopularElement
    {
        if ($value !== null && !self::isValid($value)) {
            throw new InvalidValueException($value . ' is not a valid value for ' . self::class);
        }

        return new MostPopularElement($value);
    }

    public static function DAY(): MostPopularElement
    {
        return new MostPopularElement(static::DAY);
    }

    public static function WEEK(): MostPopularElement
    {
        return new MostPopularElement(static::WEEK);
    }

    public static function MONTH(): MostPopularElement
    {
        return new MostPopularElement(static::MONTH);
    }

    public static function YEAR(): MostPopularElement
    {
        return new MostPopularElement(static::YEAR);
    }

    public static function ALL(): MostPopularElement
    {
        return new MostPopularElement(static::ALL);
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
