<?php

namespace Youkok\Biz\Pools;

use Youkok\Biz\Exceptions\PoolException;
use Youkok\Biz\Pools\Containers\ElementPoolContainer;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\SelectStatements;

class ElementPool
{
    private static $POOL;

    public static function init()
    {
        static::$POOL = [];
    }

    public static function add(ElementPoolContainer $elementPoolContainer): void
    {
        static::ensurePoolInitiated();

        static::$POOL[] = $elementPoolContainer;
    }

    public static function contains(array $attributes, SelectStatements $selectStatements): bool
    {
        static::ensurePoolInitiated();

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
        static::ensurePoolInitiated();

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
        static::ensurePoolInitiated();

        static::$POOL = [];
    }

    private static function ensurePoolInitiated(): void
    {
        if (!is_array(static::$POOL)) {
            throw new PoolException('Pool not initiated.');
        }
    }
}
