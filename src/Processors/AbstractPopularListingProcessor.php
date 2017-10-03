<?php
namespace Youkok\Processors;


use Youkok\Helpers\SessionHandler;

abstract class AbstractPopularListingProcessor
{
    public static function fromSessionHandler(SessionHandler $sessionHandler, $key)
    {
        $frontpageSettings = $sessionHandler->getDataWithKey('frontpage');
        if ($frontpageSettings === null or !is_array($frontpageSettings)) {
            return static::fromDelta();
        }

        if (!isset($frontpageSettings[$key])) {
            return static::fromDelta();
        }

        return static::fromDelta($frontpageSettings[$key]);
    }
}
