<?php
namespace Youkok;

use \Slim\App as App;

use Youkok\Helpers\JobRunner;
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

    public function runJobs($mode = JobRunner::CRON_JOB)
    {
        $this->dependencies();

        $jobRunner = new JobRunner($this->app->getContainer());
        $jobRunner->run($mode);
    }

    private function routes()
    {
        $app = $this->app;

        $app->get('/', '\Youkok\Views\Frontpage:view')->setName('home')->add(new TimingMiddleware());

        $app->group('/', function () use ($app) {
            $app->get('emner[/{params:.*}]', '\Youkok\Views\Archive:view')->setName('archive');
            $app->get('redirect/{id:[0-9]+}', '\Youkok\Views\Redirect:view')->setName('redirect');
            $app->get('last-ned/[{params:.*}]', '\Youkok\Views\Download:view')->setName('download');
            $app->get('sok', '\Youkok\Views\Search:view')->setName('search');
            $app->get('hjelp', '\Youkok\Views\Flat:help')->setName('help');
            $app->get('om', '\Youkok\Views\Flat:about')->setName('about');
            $app->get('changelog.txt', '\Youkok\Views\Flat:changelog')->setName('changelog');
            $app->get('retningslinjer', '\Youkok\Views\Flat:terms')->setName('terms');

            $app->get('lorem', '\Youkok\Views\Admin\Login:display')->setName('admin_login');
            $app->post('lorem', '\Youkok\Views\Admin\Login:submit')->setName('admin_login_submit');
        })->add(new TimingMiddleware());

        $app->group('/processors', function () use ($app) {
            $app->get('/history/{id:[0-9]+}', '\Youkok\Views\Processors\ArchiveHistory:view');
            $app->get('/autocomplete', '\Youkok\Views\Processors\Autocomplete:view');
            $app->post(
                '/favorite',
                '\Youkok\Views\Processors\ToggleFavorite:view'
            )->setName('toggle_favorite');
            $app->post(
                '/clear-frontpage-box',
                '\Youkok\Views\Processors\ClearFrontpageBox:view'
            )->setName('clear_frontpage_box');

            $app->get(
                '/popular-courses/{delta:[0-9]{1}}',
                '\Youkok\Views\Processors\PopularListing\PopularCourses:fetch'
            )->setName('popular_courses_get');
            $app->post(
                '/popular-courses',
                '\Youkok\Views\Processors\PopularListing\PopularCourses:update'
            )->setName('popular_courses');

            $app->get(
                '/popular-elements/{delta:[0-9]{1}}',
                '\Youkok\Views\Processors\PopularListing\PopularElements:fetch'
            )->setName('popular_elements_get');
            $app->post(
                '/popular-elements',
                '\Youkok\Views\Processors\PopularListing\PopularElements:update'
            )->setName('popular_elements');
            $app->get(
                '/newest-elements',
                '\Youkok\Views\Processors\PopularListing\NewestElements:view'
            )->setName('newest_elements');

            $app->group('/link', function () use ($app) {
                $app->post('/title', '\Youkok\Views\Processors\Link\FetchTitle:view');
                $app->post(
                    '/create',
                    '\Youkok\Views\Processors\Create\CreateLink:view'
                )->setName('link_submit');
            });

            $app->group('file', function () use ($app) {
                $app->post(
                    '/upload',
                    '\Youkok\Views\Processors\Create\UploadFile:view'
                )->setName('upload_file');
            });

            $app->group('/admin', function () use ($app) {
                $app->get(
                    '/homeboxes',
                    '\Youkok\Views\Processors\Admin\Homeboxes:view'
                )->setName('admin_processor_homeboxes');
            }); // TODO add middleware for admin here
        })->add(new TimingMiddleware());

        $app->group('/admin', function () use ($app) {
            $app->get(
                '',
                '\Youkok\Views\Admin\Home:view'
            )->setName('admin_home');
            $app->get(
                '/ventende',
                '\Youkok\Views\Admin\Pending:view'
            )->setName('admin_pending');
            $app->get(
                '/filer',
                '\Youkok\Views\Admin\Files:view'
            )->setName('admin_files');
            $app->get(
                '/statistikk',
                '\Youkok\Views\Admin\Statistics:view'
            )->setName('admin_statistics');
            $app->get(
                '/diagnostikk',
                '\Youkok\Views\Admin\Diagnostics:view'
            )->setName('admin_diagnostics');
            $app->get(
                '/logger',
                '\Youkok\Views\Admin\Logs:view'
            )->setName('admin_logs');
            $app->get(
                '/scripts',
                '\Youkok\Views\Admin\Scripts:view'
            )->setName('admin_scripts');
        }); // TODO add middleware for admin here
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
