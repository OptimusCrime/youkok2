<?php
declare(strict_types=1);

namespace Youkok;

use \Slim\App as App;

class Youkok
{

    private $app;

    public function __construct(array $settings)
    {
        session_start();

        $this->app = new App($settings);
    }

    public function run()
    {
        $this->routes();
        $this->middlewares();
        $this->dependencies();

        $this->app->run();
    }

    private function routes()
    {
        $this->app->get('/', '\Youkok\Views\Frontpage:view');
        $this->app->get('/hello[/{name}]', function ($request, $response, $args) {
            $response->write("Hello, " . $args['name']);
            return $response;
        })->setArgument('name', 'World!');
    }

    private function middlewares()
    {

    }

    private function dependencies() {

    }
}
