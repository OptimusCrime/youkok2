<?php
namespace Youkok\Processors;

use Youkok\Helpers\SessionHandler;
use Youkok\Models\Element;

abstract class AbstractElementFactoryProcessor
{
    protected $element;
    protected $sessionHandler;
    protected $cache;

    protected function __construct(Element $element)
    {
        $this->element = $element;
        return $this;
    }

    public function withSessionHandler(SessionHandler $sessionHandler)
    {
        $this->sessionHandler = $sessionHandler;
        return $this;
    }

    public function withCache($cache)
    {
        $this->cache = $cache;
        return $this;
    }

    abstract public function run();

    abstract public static function fromElement(Element $element);
}
