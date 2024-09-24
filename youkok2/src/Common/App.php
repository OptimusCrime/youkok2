<?php
namespace Youkok\Common;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\App as Slim;
use Monolog\Logger as MonologLogger;

use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Youkok\Common\Containers\Cache;
use Youkok\Common\Containers\Database;
use Youkok\Common\Containers\InternalServerError;
use Youkok\Common\Containers\Logger;
use Youkok\Common\Containers\PageNotFound;
use Youkok\Common\Containers\Services;
use Youkok\Common\Middlewares\AdminAuthMiddleware;
use Youkok\Common\Middlewares\ReverseProxyMiddleware;
use Youkok\Biz\Services\Job\JobService;
use Youkok\Rest\Endpoints\Admin\AdminLookupEndpoint;
use Youkok\Rest\Endpoints\Admin\AdminPendingNumEndpoint;
use Youkok\Rest\Endpoints\Admin\AdminLogOutEndpoint;
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

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function __construct()
    {
        $container = new Container();

        Database::load($container);
        Logger::load($container);
        InternalServerError::load($container);
        PageNotFound::load($container);
        Cache::load($container);
        Services::load($container);

        AppFactory::setContainer($container);

        $this->app = AppFactory::create();

        $this->routes();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function run(): void
    {
        try {
            $this->app->run();
        } catch (Exception $ex) {
            /** @var MonologLogger $logger */
            $logger = $this->app->getContainer()->get('logger');

            $logger->error($ex);

            // Rethrow exception to the outer exception handler
            throw $ex;
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function runCronJobs(): void
    {
        /** @var JobService $jobRunner */
        $jobRunner = $this->app->getContainer()->get(JobService::class);

        $jobRunner->run();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function routes(): void
    {
        $app = $this->app;
        $app->add(new ReverseProxyMiddleware());

        $app->group('', function (RouteCollectorProxy $group) use ($app) {
            // These routes are never actually accessed, they only server to make it easier to create the
            // routes with the router component.
            $group->get('/', Noop::class . ':view')->setName('home');
            $group->get('/emner', Noop::class . ':view')->setName('courses');
            $group->get('/emner/{course:[^/]+}[/{path:.+}]', Noop::class . ':view')->setName('archive');
            $group->get('/hjelp', Noop::class . ':help')->setName('help');
            $group->get('/om', Noop::class . ':about')->setName('about');
            $group->get('/changelog.txt', Noop::class . ':changelog')->setName('changelog');
            $group->get('/retningslinjer', Noop::class . ':terms')->setName('terms');
            $group->get('/lorem', Noop::class . ':view')->setName('admin_login');

            // Keep these
            $group->get('/redirect/{id:[0-9]+}', Redirect::class . ':view')->setName('redirect');
            $group->get('/last-ned/{uri:.*}', Download::class . ':view')->setName('download');
        });

        $app->group('/admin', function (RouteCollectorProxy $group) {
            $group->get('', Noop::class . ':view')->setName('admin_home');
            $group->get('/ventende', Noop::class . ':view')->setName('admin_pending');
            $group->get('/filer', Noop::class . ':view')->setName('admin_files');
            $group->get('/filer/{id:[0-9]+}', Noop::class . ':viewOne')->setName('admin_file');
            $group->get('/diagnostikk', Noop::class . ':view')->setName('admin_diagnostics');
        })->add(new AdminAuthMiddleware($app->getContainer()));

        $app->group('/rest', function (RouteCollectorProxy $group) use ($app) {
            $group->post('/login', LoginEndpoint::class . ':post');

            $group->group('/frontpage', function (RouteCollectorProxy $group) {
                $group->group('/popular', function (RouteCollectorProxy $group) {
                    $group->get('/elements', FrontpageEndpoint::class . ':popularElements');
                    $group->get('/courses', FrontpageEndpoint::class . ':popularCourses');
                });

                $group->group('/last', function (RouteCollectorProxy $group) {
                    $group->get('/visited', FrontpageEndpoint::class . ':lastVisited');
                    $group->get('/downloaded', FrontpageEndpoint::class . ':lastDownloaded');
                });

                $group->get('/boxes', FrontpageEndpoint::class . ':boxes');
                $group->get('/newest', FrontpageEndpoint::class . ':newest');
            });

            $group->get('/courses', CoursesEndpoint::class . ':get');

            $group->group('/archive', function (RouteCollectorProxy $group) {
                $group->get('/data', ArchiveEndpoint::class . ':data');
                $group->get('/content', ArchiveEndpoint::class . ':content');
            });

            $group->group('/sidebar', function (RouteCollectorProxy $group) {
                $group->group('/post', function (RouteCollectorProxy $group) {
                    $group->group('/create', function (RouteCollectorProxy $group) {
                        $group->put('/link', CreateLinkEndpoint::class . ':put');
                        $group->post('/file/{id:[0-9]+}', CreateFileEndpoint::class . ':post');
                    });

                    $group->put('/title', TitleFetchEndpoint::class . ':put');
                });

                $group->get('/popular', MostPopularEndpoint::class . ':get');
                $group->get('/history/{id:[0-9]+}', ArchiveHistoryEndpoint::class . ':get');
            });

            $group->group('/admin', function (RouteCollectorProxy $group) use ($app) {
                $group->group('/home', function (RouteCollectorProxy $group) {
                    $group->get('/boxes', AdminHomeBoxesEndpoint::class . ':get');
                    $group->get('/graph', AdminHomeGraphEndpoint::class . ':get');
                });

                $group->get('/pending/num', AdminPendingNumEndpoint::class . ':get');
                $group->get('/lookup', AdminLookupEndpoint::class . ':get');
                $group->get('/log-out', AdminLogOutEndpoint::class . ':get');

                $group->group('/files', function (RouteCollectorProxy $group) {
                    $group->get('', AdminFilesEndpoint::class . ':list');
                    $group->get('/{id:[0-9]+}', AdminFilesEndpoint::class . ':get');
                    $group->put('/{id:[0-9]+}', AdminFilesEndpoint::class . ':put');
                    $group->get('/pending', AdminFilesPendingEndpoint::class . ':get');
                    $group->put('/directory', AdminFilesDirectoryEndpoint::class . ':get');
                    $group->get('/single/{id:[0-9]+}', AdminFilesEndpoint::class . ':listSingle');
                });

                $group->group('/diagnostics', function (RouteCollectorProxy $group) {
                    $group->get('/cache', AdminRedisCache::class . ':get');
                });
            })->add(new AdminAuthMiddleware($app->getContainer()));
        });
    }
}
