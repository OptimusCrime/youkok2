<?php

namespace Youkok\Biz\Pools;

use Youkok\Biz\Exceptions\PoolException;
use Youkok\Biz\Pools\Containers\ElementPoolContainer;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\SelectStatements;

class ElementPool
{
    private static $DISABLED = true;
    private static $POOL = [];

    public static function init()
    {
        static::$DISABLED = false;
    }

    public static function disable(): void
    {
        static::$DISABLED = true;
    }

    public static function add(ElementPoolContainer $elementPoolContainer): void
    {
        if (!static::$DISABLED) {
            static::$POOL[] = $elementPoolContainer;
        }
    }

    public static function contains(array $attributes, SelectStatements $selectStatements): bool
    {
        if (static::$DISABLED) {
            return false;
        }

        /** @var ElementPoolContainer $poolContainer */
        foreach (static::$POOL as $poolContainer) {
            if ($poolContainer->equals($attributes, $selectStatements)) {
                return true;
            }
        }

        return false;
    }

    public static function get(array $attributes, SelectStatements $selectStatements): Element
    {
        if (static::$DISABLED) {
            throw new PoolException('Not found in pool');
        }

        /** @var ElementPoolContainer $poolContainer */
        foreach (static::$POOL as $poolContainer) {
            if ($poolContainer->equals($attributes, $selectStatements)) {
                return $poolContainer->getElement();
            }
        }

        throw new PoolException('Not found in pool');
    }

    public static function reset(): void
    {
        static::$POOL = [];
    }
}
