<?php
namespace Youkok\Common;

use Slim\App as Slim;

use Youkok\Common\Containers\Cache;
use Youkok\Common\Containers\Database;
use Youkok\Common\Containers\InternalServerError;
use Youkok\Common\Containers\PageNotFound;
use Youkok\Common\Containers\Services;
use Youkok\Common\Containers\View;
use Youkok\Common\Middlewares\AdminAuthMiddleware;
use Youkok\Common\Middlewares\ReverseProxyMiddleware;
use Youkok\Common\Middlewares\TimingMiddleware;
use Youkok\Biz\Services\Job\JobService;
use Youkok\Rest\Endpoints\Archive as ArchiveRest;
use Youkok\Rest\Endpoints\Frontpage as FrontpageRest;
use Youkok\Rest\Endpoints\Sidebar\MostPopular;
use Youkok\Web\Views\Archive;
use Youkok\Web\Views\Courses;
use Youkok\Web\Views\Download;
use Youkok\Web\Views\Flat;
use Youkok\Web\Views\Frontpage;
use Youkok\Web\Views\Redirect;
use Youkok\Web\Views\Admin\Diagnostics;
use Youkok\Web\Views\Admin\Files;
use Youkok\Web\Views\Admin\Home;
use Youkok\Web\Views\Admin\Login;
use Youkok\Web\Views\Admin\Logs;
use Youkok\Web\Views\Admin\Pending;
use Youkok\Web\Views\Admin\Scripts;
use Youkok\Web\Views\Admin\Statistics;

class App
{
    private $app;

    public function __construct(array $settings)
    {
        session_start();

        $this->app = new Slim($settings);
    }

    public function run(): void
    {
        $this->setup();

        try {
            $this->app->run();
        } catch (\Exception $e) {
            // TODO
        }
    }

    public function runJobs($mode = JobService::CRON_JOB, $code = null): void
    {
        $this->setup();

        $jobRunner = $this->app->getContainer()->get(JobService::class);

        if ($mode == JobService::SPECIFIC_JOB && $code !== null) {
            $jobRunner->runCode($code);
        }
        else {
            $jobRunner->run($mode);
        }
    }

    private function setup(): void
    {
        $this->dependencies();
        $this->routes();
    }

    private function routes(): void
    {
        $app = $this->app;

        $app->group('/', function () use ($app) {
            $app->get('', Frontpage::class . ':view')->setName('home');
            $app->get('emner', Courses::class . ':view')->setName('courses');
            $app->get('emner/{course:[^/]+}[/{params:.+}]', Archive::class . ':view')->setName('archive');
            $app->get('redirect/{id:[0-9]+}', Redirect::class . ':view')->setName('redirect');
            $app->get('last-ned/{uri:.*}', Download::class . ':view')->setName('download');
            $app->get('hjelp', Flat::class . ':help')->setName('help');
            $app->get('om', Flat::class . ':about')->setName('about');
            $app->get('changelog.txt', Flat::class . ':changelog')->setName('changelog');
            $app->get('retningslinjer', Flat::class . ':terms')->setName('terms');

            $app->get('lorem', Login::class . ':display')->setName('admin_login');
            $app->post('lorem', Login::class . ':submit')->setName('admin_login_submit');
        })->add(new TimingMiddleware())->add(new ReverseProxyMiddleware());

        $app->group('/admin', function () use ($app) {
            $app->get('', Home::class . ':view')->setName('admin_home');
            $app->get('/ventende', Pending::class . ':view')->setName('admin_pending');
            $app->get('/filer', Files::class . ':view')->setName('admin_files');
            $app->get('/statistikk', Statistics::class . ':view')->setName('admin_statistics');
            $app->get('/diagnostikk', Diagnostics::class . ':view')->setName('admin_diagnostics');
            $app->get('/logger', Logs::class . ':view')->setName('admin_logs');
            $app->get('/scripts', Scripts::class . ':view')->setName('admin_scripts');
        })->add(new ReverseProxyMiddleware())->add(new AdminAuthMiddleware($app->getContainer()));

        $app->group('/rest', function () use ($app) {
            $app->get('/frontpage', FrontpageRest::class . ':get');
            $app->put('/frontpage', FrontpageRest::class . ':put');

            $app->get('/archive/{id:[0-9]+}', ArchiveRest::class . ':get');

            $app->group('/sidebar', function () use ($app) {
               $app->get('/popular', MostPopular::class . ':get');
            });
        })->add(new TimingMiddleware())->add(new ReverseProxyMiddleware());

        $app->group('/processors', function () use ($app) {
            // TODO
            $app->get('/history/{id:[0-9]+}', '\Youkok\Views\Processors\ArchiveHistory:view');

            // TODO
            $app->get('/autocomplete', '\Youkok\Views\Processors\Autocomplete:view');

            $app->group('/link', function () use ($app) {
                $app->post('/title', '\Youkok\Views\Processors\Link\FetchTitle:view');
                $app->post('/create', '\Youkok\Views\Processors\Create\CreateLink:view')->setName('link_submit');
            });

            $app->group('file', function () use ($app) {
                $app->post('/upload', '\Youkok\Views\Processors\Create\UploadFile:view')->setName('upload_file');
            });

            $app->group('/admin', function () use ($app) {
                $app->get('/homeboxes', '\Youkok\Views\Processors\Admin\Homeboxes:view')->setName('admin_processor_homeboxes');
                $app->get('/homegraph', '\Youkok\Views\Processors\Admin\HomeGraph:view')->setName('admin_processor_homegraph');
                $app->get('/element-details/{id:[0-9]+}', '\Youkok\Views\Processors\Admin\ElementDetails:get')->setName('admin_processor_element_details_fetch');
                $app->put('/element-details', '\Youkok\Views\Processors\Admin\ElementDetails:update')->setName('admin_processor_element_details_update');
                $app->get('/element-markup/{id:[0-9]+}', '\Youkok\Views\Processors\Admin\ElementListMarkup:get')->setName('admin_processor_element_list_markup_fetch');
                $app->get('/element-markup-pending/{id:[0-9]+}', '\Youkok\Views\Processors\Admin\ElementListPendingMarkup:get')->setName('admin_processor_element_list_pending_markup_fetch');
                $app->post('/element-create', '\Youkok\Views\Processors\Admin\ElementCreate:run')->setName('admin_processor_element_create');
                $app->put('/element-regenerate/uri', '\Youkok\Views\Processors\Admin\ElementRegenerate:uri')->setName('admin_processor_element_regenerate_uri');
            })->add(new AdminAuthMiddleware($app->getContainer()));
        })->add(new TimingMiddleware())->add(new ReverseProxyMiddleware());
    }

    private function dependencies(): void
    {
        $containers = [
            Cache::class,
            Database::class,
            InternalServerError::class,
            PageNotFound::class,
            Services::class,
            View::class,
        ];

        foreach ($containers as $container) {
            call_user_func([$container, 'load'], $this->app->getContainer());
        }
    }
}
