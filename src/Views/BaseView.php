<?php
declare(strict_types=1);

namespace Youkok\Views;

use \Slim\Container;

class BaseView
{
    protected $ci;

    public function __construct(Container $ci)
    {
        $this->ci = $ci;
    }
}
