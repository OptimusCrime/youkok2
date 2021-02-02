<?php
namespace Youkok\Common;

use Exception;
use Slim\App as Slim;
use Monolog\Logger as MonologLogger;

use Slim\Exception\MethodNotAllowedException;
use Slim\Exception\NotFoundException;
use Youkok\Biz\Pools\ElementPool;
use Youkok\Common\Containers\Cache;
use Youkok\Common\Containers\Database;
use Youkok\Common\Containers\InternalServerError;
use Youkok\Common\Containers\Logger;
use Youkok\Common\Containers\PageNotFound;
use Youkok\Common\Containers\Services;
use Youkok\Common\Middlewares\AdminAuthMiddleware;
use Youkok\Common\Middlewares\ReverseProxyMiddleware;
use Youkok\Biz\Services\Job\JobService;
use Youkok\Rest\Endpoints\Admin\Diagnostics\AdminRedisCache;
use Youkok\Rest\Endpoints\Admin\Files\AdminFilesDirectoryEndpoint;
use Youkok\Rest\Endpoints\Admin\Files\AdminFilesEndpoint;
use Youkok\Rest\Endpoints\Admin\Files\AdminFilesPendingEndpoint;
use Youkok\Rest\Endpoints\Admin\Home\AdminHomeBoxesEndpoint;
use Youkok\Rest\Endpoints\Admin\Home\AdminHomeGraphEndpoint;
use Youkok\Rest\Endpoints\ArchiveEndpoint;
use Youkok\Rest\Endpoints\CoursesEndpoint;
use Youkok\Rest\Endpoints\FrontpageEndpoint;
use Youkok\Rest\Endpoints\LoginEndpoint;
use Youkok\Rest\Endpoints\Sidebar\ArchiveHistoryEndpoint;
use Youkok\Rest\Endpoints\Sidebar\MostPopularEndpoint;
use Youkok\Rest\Endpoints\Sidebar\Post\Create\CreateFileEndpoint;
use Youkok\Rest\Endpoints\Sidebar\Post\Create\CreateLinkEndpoint;
use Youkok\Rest\Endpoints\Sidebar\Post\TitleFetchEndpoint;
use Youkok\Web\Views\Download;
use Youkok\Web\Views\Noop;
use Youkok\Web\Views\Redirect;

class App
{
    private Slim $app;

    public function __construct(array $settings)
    {
        $this->app = new Slim($settings);
    }

    /**
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     */
    public function run(): void
    {
        $this->setup();
        $this->startPools();

        try {
            $this->app->run();
        } catch (Exception $ex) {
            /** @var MonologLogger $logger */
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

    public function runCronJobs(): void
    {
        $this->setup();

        /** @var JobService $jobRunner */
        $jobRunner = $this->app->getContainer()->get(JobService::class);

        $jobRunner->run();
    }

    private function setup(): void
    {
        $this->dependencies();
        $this->routes();
    }

    private function routes(): void
    {
        $app = $this->app;
        $app->add(new ReverseProxyMiddleware());

        $app->group('', function () use ($app) {
            // These routes are never actually accessed, they only server to make it easier to create the
            // routes with the router component.
            $app->get('/', Noop::class . ':view')->setName('home');
            $app->get('/emner', Noop::class . ':view')->setName('courses');
            $app->get('/emner/{course:[^/]+}[/{path:.+}]', Noop::class . ':view')->setName('archive');
            $app->get('/hjelp', Noop::class . ':help')->setName('help');
            $app->get('/om', Noop::class . ':about')->setName('about');
            $app->get('/changelog.txt', Noop::class . ':changelog')->setName('changelog');
            $app->get('/retningslinjer', Noop::class . ':terms')->setName('terms');
            $app->get('/lorem', Noop::class . ':view')->setName('admin_login');

            // Keep these
            $app->get('/redirect/{id:[0-9]+}', Redirect::class . ':view')->setName('redirect');
            $app->get('/last-ned/{uri:.*}', Download::class . ':view')->setName('download');

            // TODO: Handle redirects in nginx /kokeboka/ and /emner/kokeboka/
        });

        $app->group('/admin', function () use ($app) {
            $app->get('', Noop::class . ':view')->setName('admin_home');
            $app->get('/ventende', Noop::class . ':view')->setName('admin_pending');
            $app->get('/filer', Noop::class . ':view')->setName('admin_files');
            $app->get('/filer/{id:[0-9]+}', Noop::class . ':viewOne')->setName('admin_file');
            $app->get('/diagnostikk', Noop::class . ':view')->setName('admin_diagnostics');
        })->add(new AdminAuthMiddleware($app->getContainer()));

        $app->group('/rest', function () use ($app) {
            $app->post('/login', LoginEndpoint::class . ':post');
            $app->group('/frontpage', function () use ($app) {
                $app->group('/popular', function () use ($app) {
                    $app->get('/elements', FrontpageEndpoint::class . ':popularElements');
                    $app->get('/courses', FrontpageEndpoint::class . ':popularCourses');
                });
                $app->group('/last', function () use ($app) {
                    $app->get('/visited', FrontpageEndpoint::class . ':lastVisited');
                    $app->get('/downloaded', FrontpageEndpoint::class . ':lastDownloaded');
                });

                $app->get('/boxes', FrontpageEndpoint::class . ':boxes');
                $app->get('/newest', FrontpageEndpoint::class . ':newest');
            });

            $app->post('/courses', CoursesEndpoint::class . ':post');

            $app->group('/archive', function () use ($app) {
                $app->get('/data', ArchiveEndpoint::class . ':data');
                $app->get('/content', ArchiveEndpoint::class . ':content');
            });

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
            Logger::class,
        ];

        foreach ($containers as $container) {
            call_user_func([$container, 'load'], $this->app->getContainer());
        }
    }
}
