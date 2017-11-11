<?php
namespace Youkok\Processors;

use Youkok\Helpers\SessionHandler;

class UpdateUserMostPopularProcessor
{
    private $sessionHandler;
    private $delta;
    private $key;
    private $enums;

    private function __construct($sessionHandler)
    {
        $this->sessionHandler = $sessionHandler;
    }

    public function withDelta($delta)
    {
        $this->delta = (int) $delta;
        return $this;
    }

    public function withKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function withEnums($enums)
    {
        $this->enums = $enums;
        return $this;
    }

    public static function fromSessionHandler($sessionHandler)
    {
        return new UpdateUserMostPopularProcessor($sessionHandler);
    }

    public function run()
    {
        if (!in_array($this->delta, $this->enums)) {
            return false;
        }

        return static::updateUserMostPopular($this->sessionHandler, $this->key, $this->delta);
    }

    private static function updateUserMostPopular(SessionHandler $sessionHandler, $key, $delta)
    {
        return $sessionHandler->forceSetData($key, $delta, SessionHandler::MODE_OVERWRITE);
    }
}
