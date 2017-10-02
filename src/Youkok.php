<?php
namespace Youkok;

use \Slim\App as App;

use Youkok\Loaders\Containers;
use Youkok\Middlewares\TimingMiddleware;

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
        $this->dependencies();

        $this->app->run();
    }

    private function routes()
    {
        $app = $this->app;

        $app->get('/', '\Youkok\Views\Frontpage:view')->setName('home')->add(new TimingMiddleware());

        $app->group('/', function() use ($app) {
            $app->get('emner[/{params:.*}]', '\Youkok\Views\Archive:view')->setName('archive');
            $app->get('redirect/{id:[0-9]+}', '\Youkok\Views\Redirect:view')->setName('redirect');
            $app->get('last-ned/[{params:.*}]', '\Youkok\Views\Download:view')->setName('download');
            $app->get('sok', '\Youkok\Views\Search:view')->setName('search');
            $app->get('hjelp', '\Youkok\Views\Flat:help')->setName('help');
            $app->get('om', '\Youkok\Views\Flat:about')->setName('about');
            $app->get('changelog.txt', '\Youkok\Views\Flat:changelog')->setName('changelog');
            $app->get('retningslinjer', '\Youkok\Views\Flat:terms')->setName('terms');
        })->add(new TimingMiddleware());

        $app->group('/processors', function () use ($app) {
            $app->get('/popular-courses/{delta:[0-9]{1}}', '\Youkok\Views\Processors\PopularCourses:view');
            $app->get('/newest-elements', '\Youkok\Views\Processors\NewestElements:view');
            $app->get('/history/{id:[0-9]+}', '\Youkok\Views\Processors\ArchiveHistory:view');
            $app->post('/favorite', '\Youkok\Views\Processors\ToggleFavorite:view');

            $app->group('/link', function () use ($app) {
                $app->post('/title', '\Youkok\Views\Processors\Link\FetchTitle:view');
                $app->post('/create', '\Youkok\Views\Processors\Create\CreateLink:view');
            });

        })->add(new TimingMiddleware());
    }

    private function dependencies()
    {
        $containers = [
            \Youkok\Containers\View::class,
            \Youkok\Containers\Database::class,
            \Youkok\Containers\Cache::class,
        ];

        foreach ($containers as $container) {
            call_user_func([$container, 'load'], $this->app->getContainer());
        }
    }
}
