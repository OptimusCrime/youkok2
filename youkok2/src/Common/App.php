<?php

namespace Youkok\Common;

use Slim\App as Slim;

use Youkok\Biz\Pools\ElementPool;
use Youkok\Common\Containers\Cache;
use Youkok\Common\Containers\Database;
use Youkok\Common\Containers\InternalServerError;
use Youkok\Common\Containers\Logger;
use Youkok\Common\Containers\PageNotFound;
use Youkok\Common\Containers\Services;
use Youkok\Common\Containers\View;
use Youkok\Common\Middlewares\AdminAuthMiddleware;
use Youkok\Common\Middlewares\DumpSqlLogMiddleware;
use Youkok\Common\Middlewares\ReverseProxyMiddleware;
use Youkok\Common\Middlewares\TimingMiddleware;
use Youkok\Biz\Services\Job\JobService;
use Youkok\Rest\Endpoints\ArchiveEndpoint;
use Youkok\Rest\Endpoints\FrontpageEndpoint;
use Youkok\Rest\Endpoints\Sidebar\ArchiveHistoryEndpoint;
use Youkok\Rest\Endpoints\Sidebar\MostPopularEndpoint;
use Youkok\Web\Views\Archive;
use Youkok\Web\Views\Courses;
use Youkok\Web\Views\Download;
use Youkok\Web\Views\Flat;
use Youkok\Web\Views\Frontpage;
use Youkok\Web\Views\Redirect;
use Youkok\Web\Views\Admin\AdminDiagnostics;
use Youkok\Web\Views\Admin\Files;
use Youkok\Web\Views\Admin\AdminHome;
use Youkok\Web\Views\Admin\Login;
use Youkok\Web\Views\Admin\AdminLogs;
use Youkok\Web\Views\Admin\Pending;
use Youkok\Web\Views\Admin\AdminScripts;
use Youkok\Web\Views\Admin\AdminStatistics;

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
        $this->startPools();

        try {
            $this->app->run();
        } catch (\Exception $e) {
            // TODO: LOgging

            // Rethrow exception to the outer exception handler
            throw $e;
        }
    }

    public function runJobs($mode = JobService::CRON_JOB, $code = null): void
    {
        $this->setup();

        /** @var JobService $jobRunner */
        $jobRunner = $this->app->getContainer()->get(JobService::class);

        if ($mode == JobService::SPECIFIC_JOB && $code !== null) {
            $jobRunner->runCode($code);
        } else {
            $jobRunner->run($mode);
        }
    }

    private function setup(): void
    {
        $this->dependencies();
        $this->routes();
    }

    private function startPools(): void
    {
        ElementPool::init();
    }

    private function routes(): void
    {
        $app = $this->app;

        $app->group('/', function () use ($app) {
            $app->get('', Frontpage::class . ':view')->setName('home');
            $app->get('emner', Courses::class . ':view')->setName('courses');
            $app->get('emner/{course:[^/]+}[/{path:.+}]', Archive::class . ':view')->setName('archive');
            $app->get('redirect/{id:[0-9]+}', Redirect::class . ':view')->setName('redirect');
            $app->get('last-ned/{uri:.*}', Download::class . ':view')->setName('download');
            $app->get('hjelp', Flat::class . ':help')->setName('help');
            $app->get('om', Flat::class . ':about')->setName('about');
            $app->get('changelog.txt', Flat::class . ':changelog')->setName('changelog');
            $app->get('retningslinjer', Flat::class . ':terms')->setName('terms');

            $app->get('lorem', Login::class . ':view')->setName('admin_login');
            $app->post('lorem', Login::class . ':post')->setName('admin_login_submit');
        })->add(new TimingMiddleware())->add(new ReverseProxyMiddleware())->add(new DumpSqlLogMiddleware());

        $app->group('/admin', function () use ($app) {
            $app->get('', AdminHome::class . ':view')->setName('admin_home');
            $app->get('/ventende', Pending::class . ':view')->setName('admin_pending');
            $app->get('/filer', Files::class . ':view')->setName('admin_files');
            $app->get('/statistikk', AdminStatistics::class . ':view')->setName('admin_statistics');
            $app->get('/diagnostikk', AdminDiagnostics::class . ':view')->setName('admin_diagnostics');
            $app->get('/logger', AdminLogs::class . ':view')->setName('admin_logs');
            $app->get('/scripts', AdminScripts::class . ':view')->setName('admin_scripts');
        })->add(new ReverseProxyMiddleware())->add(new AdminAuthMiddleware($app->getContainer()));

        $app->group('/rest', function () use ($app) {
            $app->get('/frontpage/boxes', FrontpageEndpoint::class . ':boxes');
            $app->get('/frontpage/popular/elements', FrontpageEndpoint::class . ':popularElements');
            $app->get('/frontpage/popular/courses', FrontpageEndpoint::class . ':popularCourses');
            $app->get('/frontpage/newest', FrontpageEndpoint::class . ':newest');
            $app->get('/frontpage/last/visited', FrontpageEndpoint::class . ':lastVisited');
            $app->get('/frontpage/last/downloaded', FrontpageEndpoint::class . ':lastDownloaded');
            $app->put('/frontpage', FrontpageEndpoint::class . ':put');

            $app->get('/archive/{id:[0-9]+}', ArchiveEndpoint::class . ':get');

            $app->group('/sidebar', function () use ($app) {
                $app->get('/popular', MostPopularEndpoint::class . ':get');
                $app->get('/history/{id:[0-9]+}', ArchiveHistoryEndpoint::class . ':get');
            });
        })->add(new TimingMiddleware())->add(new ReverseProxyMiddleware())->add(new DumpSqlLogMiddleware());

        // TODO remove
        $app->group('/processors', function () use ($app) {
            $app->group('/link', function () use ($app) {
                $app->post('/title', '\Youkok\Views\Processors\Link\FetchTitle:view');
                $app->post('/create', '\Youkok\Views\Processors\Create\CreateLink:view')->setName('link_submit');
            });

            $app->group('file', function () use ($app) {
                $app->post('/upload', '\Youkok\Views\Processors\Create\UploadFile:view')->setName('upload_file');
            });

            $app->group('/admin', function () use ($app) {

                // TODO
                $app->get(
                    '/homeboxes',
                    '\Youkok\Views\Processors\Admin\Homeboxes:view'
                )->setName('admin_processor_homeboxes');
                $app->get(
                    '/homegraph',
                    '\Youkok\Views\Processors\Admin\HomeGraph:view'
                )->setName('admin_processor_homegraph');
                $app->get(
                    '/element-details/{id:[0-9]+}',
                    '\Youkok\Views\Processors\Admin\ElementDetails:get'
                )->setName('admin_processor_element_details_fetch');
                $app->put(
                    '/element-details',
                    '\Youkok\Views\Processors\Admin\ElementDetails:update'
                )->setName('admin_processor_element_details_update');
                $app->get(
                    '/element-markup/{id:[0-9]+}',
                    '\Youkok\Views\Processors\Admin\ElementListMarkup:get'
                )->setName('admin_processor_element_list_markup_fetch');
                $app->get(
                    '/element-markup-pending/{id:[0-9]+}',
                    '\Youkok\Views\Processors\Admin\ElementListPendingMarkup:get'
                )->setName('admin_processor_element_list_pending_markup_fetch');
                $app->post(
                    '/element-create',
                    '\Youkok\Views\Processors\Admin\ElementCreate:run'
                )->setName('admin_processor_element_create');
                $app->put(
                    '/element-regenerate/uri',
                    '\Youkok\Views\Processors\Admin\ElementRegenerate:uri'
                )->setName('admin_processor_element_regenerate_uri');
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
            Logger::class,
        ];

        foreach ($containers as $container) {
            call_user_func([$container, 'load'], $this->app->getContainer());
        }
    }
}
