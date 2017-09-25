<?php
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
        $this->app->get('/', '\Youkok\Views\Frontpage:view')->setName('home');
        $this->app->get('/emner', '\Youkok\Views\Courses:view')->setName('courses');
        $this->app->get('/emner/[{params:.*}/]', '\Youkok\Views\Archive:view')->setName('archive');
        $this->app->get('/redirect/{id:[0-9]+}', '\Youkok\Views\Redirect:view')->setName('redirect');
        $this->app->get('/last-ned/{id:[0-9]+}', '\Youkok\Views\Download:view')->setName('download');
        $this->app->get('/sok', '\Youkok\Views\Search:view')->setName('search');
        $this->app->get('/hjelp', '\Youkok\Views\Flat:help')->setName('help');
        $this->app->get('/om', '\Youkok\Views\Flat:about')->setName('about');
        $this->app->get('/changelog.txt', '\Youkok\Views\Flat:changelog')->setName('changelog');
        $this->app->get('/retningslinjer', '\Youkok\Views\Flat:terms')->setName('terms');

        $this->app->get('/processors/popular-courses/{delta:[0-9]{1}}', '\Youkok\Views\Processors\PopularCourses:view');
        $this->app->get('/processors/newest-elements', '\Youkok\Views\Processors\NewestElements:view');
    }

    private function middlewares()
    {
        // TODO
    }

    private function dependencies()
    {
        Containers::load($this->app->getContainer(), [
            \Youkok\Containers\View::class,
            \Youkok\Containers\Database::class,
            \Youkok\Containers\Cache::class,
        ]);
    }
}
