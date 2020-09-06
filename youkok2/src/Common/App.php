<?php

namespace Youkok\Common;

use Exception;
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
use Youkok\Rest\Endpoints\Admin\Diagnostics\AdminRedisCache;
use Youkok\Rest\Endpoints\Admin\Files\AdminFilesDirectoryEndpoint;
use Youkok\Rest\Endpoints\Admin\Files\AdminFilesEndpoint;
use Youkok\Rest\Endpoints\Admin\Files\AdminFilesPendingEndpoint;
use Youkok\Rest\Endpoints\Admin\Home\AdminHomeBoxesEndpoint;
use Youkok\Rest\Endpoints\Admin\Home\AdminHomeGraphEndpoint;
use Youkok\Rest\Endpoints\ArchiveEndpoint;
use Youkok\Rest\Endpoints\FrontpageEndpoint;
use Youkok\Rest\Endpoints\Sidebar\ArchiveHistoryEndpoint;
use Youkok\Rest\Endpoints\Sidebar\MostPopularEndpoint;
use Youkok\Rest\Endpoints\Sidebar\Post\Create\CreateFileEndpoint;
use Youkok\Rest\Endpoints\Sidebar\Post\Create\CreateLinkEndpoint;
use Youkok\Rest\Endpoints\Sidebar\Post\TitleFetchEndpoint;
use Youkok\Web\Views\Archive;
use Youkok\Web\Views\Courses;
use Youkok\Web\Views\Download;
use Youkok\Web\Views\Flat;
use Youkok\Web\Views\Frontpage;
use Youkok\Web\Views\KokebokaLegacyRedirect;
use Youkok\Web\Views\Redirect;
use Youkok\Web\Views\Admin\Login;
use Youkok\Web\Views\Admin\AdminDiagnostics;
use Youkok\Web\Views\Admin\AdminHome;
use Youkok\Web\Views\Admin\AdminLogs;
use Youkok\Web\Views\Admin\AdminScripts;
use Youkok\Web\Views\Admin\AdminStatistics;
use Youkok\Web\Views\Admin\AdminFiles;
use Youkok\Web\Views\Admin\AdminPending;

class App
{
    private $app;

    public function __construct(array $settings)
    {
        $this->app = new Slim($settings);
    }

    public function run(): void
    {
        $this->setup();
        $this->startPools();

        try {
            $this->app->run();
        } catch (Exception $ex) {
            /** @var Logger $logger */
            $logger = $this->app->getContainer()->get(Logger::class);

            $logger->error($ex);

            // Rethrow exception to the outer exception handler
            throw $ex;
        }
    }

    public function startPools(): void
    {
        ElementPool::init();
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

    private function routes(): void
    {
        $app = $this->app;
        $app->add(new TimingMiddleware());
        $app->add(new ReverseProxyMiddleware());
        $app->add(new DumpSqlLogMiddleware());

        $app->group('', function () use ($app) {
            $app->get('/', Frontpage::class . ':view')->setName('home');
            $app->get('/emner', Courses::class . ':view')->setName('courses');
            $app->get('/emner/{course:[^/]+}[/{path:.+}]', Archive::class . ':view')->setName('archive');
            $app->get('/redirect/{id:[0-9]+}', Redirect::class . ':view')->setName('redirect');
            $app->get('/last-ned/{uri:.*}', Download::class . ':view')->setName('download');
            $app->get('/hjelp', Flat::class . ':help')->setName('help');
            $app->get('/om', Flat::class . ':about')->setName('about');
            $app->get('/changelog.txt', Flat::class . ':changelog')->setName('changelog');
            $app->get('/retningslinjer', Flat::class . ':terms')->setName('terms');

            $app->get('/lorem', Login::class . ':view')->setName('admin_login');
            $app->post('/lorem', Login::class . ':post')->setName('admin_login_submit');

            $app->get('/kokeboka/[{path:.*}]', KokebokaLegacyRedirect::class . ':view');
        });

        $app->group('/admin', function () use ($app) {
            $app->get('', AdminHome::class . ':view')->setName('admin_home');
            $app->get('/ventende', AdminPending::class . ':view')->setName('admin_pending');
            $app->get('/filer', AdminFiles::class . ':view')->setName('admin_files');
            $app->get('/filer/{id:[0-9]+}', AdminFiles::class . ':viewOne')->setName('admin_file');
            $app->get('/statistikk', AdminStatistics::class . ':view')->setName('admin_statistics');
            $app->get('/diagnostikk', AdminDiagnostics::class . ':view')->setName('admin_diagnostics');
            $app->get('/logger', AdminLogs::class . ':view')->setName('admin_logs');
            $app->get('/scripts', AdminScripts::class . ':view')->setName('admin_scripts');
        })->add(new AdminAuthMiddleware($app->getContainer()));

        $app->group('/rest', function () use ($app) {
            $app->group('/frontpage', function () use ($app) {
                $app->group('/popular', function () use ($app) {
                    $app->get('/elements', FrontpageEndpoint::class . ':popularElements');
                    $app->get('/courses', FrontpageEndpoint::class . ':popularCourses');
                });
                $app->group('/last', function () use ($app) {
                    $app->get('/visited', FrontpageEndpoint::class . ':lastVisited');
                    $app->get('/downloaded', FrontpageEndpoint::class . ':lastDownloaded');
                });

                $app->put('', FrontpageEndpoint::class . ':put');

                $app->get('/boxes', FrontpageEndpoint::class . ':boxes');
                $app->get('/newest', FrontpageEndpoint::class . ':newest');
            });

            $app->get('/archive/{id:[0-9]+}', ArchiveEndpoint::class . ':get');

            $app->group('/sidebar', function () use ($app) {
                $app->group('/post', function () use ($app) {
                    $app->group('/create', function () use ($app) {
                        $app->put('/link', CreateLinkEndpoint::class . ':put');
                        $app->post('/file/{id:[0-9]+}', CreateFileEndpoint::class . ':post');
                    });

                    $app->put('/title', TitleFetchEndpoint::class . ':put');
                });

                $app->get('/popular', MostPopularEndpoint::class . ':get');
                $app->get('/history/{id:[0-9]+}', ArchiveHistoryEndpoint::class . ':get');
            });

            $app->group('/admin', function () use ($app) {
                $app->group('/home', function () use ($app) {
                    $app->get('/boxes', AdminHomeBoxesEndpoint::class . ':get');
                    $app->get('/graph', AdminHomeGraphEndpoint::class . ':get');
                });

                $app->group('/files', function () use ($app) {
                    $app->get('', AdminFilesEndpoint::class . ':list');
                    $app->get('/{id:[0-9]+}', AdminFilesEndpoint::class . ':get');
                    $app->put('/{id:[0-9]+}', AdminFilesEndpoint::class . ':put');
                    $app->get('/pending', AdminFilesPendingEndpoint::class . ':get');
                    $app->put('/directory', AdminFilesDirectoryEndpoint::class . ':get');
                    $app->get('/single/{id:[0-9]+}', AdminFilesEndpoint::class . ':listSingle');
                });

                $app->group('/diagnostics', function () use ($app) {
                    $app->get('/cache', AdminRedisCache::class . ':get');
                });
            })->add(new AdminAuthMiddleware($app->getContainer()));
        });
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
