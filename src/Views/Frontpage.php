<?php
declare(strict_types=1);

namespace Youkok\Views;

use \Slim\Container as Container;

class Frontpage
{
    protected $ci;

    public function __construct(Container $ci) {
        $this->ci = $ci;
    }

    public function view($request, $response, array $args) {
        echo 'dero';
    }
}
