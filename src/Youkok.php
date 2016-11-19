<?php
declare(strict_types=1);

namespace Youkok;

use \Slim\App as App;

use Youkok\Loaders\Containers;

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
    }

    private function middlewares()
    {
        // TODO
    }

    private function dependencies()
    {
        Containers::load($this->app->getContainer(), [
            \Youkok\Containers\View::class
        ]);
    }
}
