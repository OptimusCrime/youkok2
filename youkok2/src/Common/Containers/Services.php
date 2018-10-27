<?php
namespace Youkok\Common\Containers;

use Psr\Container\ContainerInterface;

use Youkok\Biz\Services\ArchiveService;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\CourseListService;
use Youkok\Biz\Services\Download\DownloadCountService;
use Youkok\Biz\Services\Download\DownloadFileInfoService;
use Youkok\Biz\Services\FrontpageService;
use Youkok\Biz\Services\Jobs\RemoveOldSessionsJobServiceService;
use Youkok\Biz\Services\Jobs\UpdateMostPopularCoursesJobService;
use Youkok\Biz\Services\Jobs\UpdateMostPopularElementsJobService;
use Youkok\Biz\Services\JobService;
use Youkok\Biz\Services\Mappers\CourseMapper;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Biz\Services\PopularListing\MostPopularCoursesService;
use Youkok\Biz\Services\PopularListing\MostPopularElementsService;
use Youkok\Biz\Services\SearchRedirectService;
use Youkok\Biz\Services\SessionService;
use Youkok\Biz\Services\Download\UpdateDownloadsService;
use Youkok\Biz\Services\UrlService;
use Youkok\Biz\Services\User\UserService;

class Services implements ContainersInterface
{
    public static function load(ContainerInterface $container)
    {
        $container[SessionService::class] = function () {
            return new SessionService();
        };

        $container[SearchRedirectService::class] = function () {
            return new SearchRedirectService();
        };

        $container[CacheService::class] = function (ContainerInterface $container) {
            return new CacheService(
                $container->get('cache')
            );
        };

        $container[FrontpageService::class] = function (ContainerInterface $container) {
            return new FrontpageService(
                $container->get(SessionService::class),
                $container->get(MostPopularCoursesService::class),
                $container->get(MostPopularElementsService::class),
                $container->get(UserService::class)
            );
        };

        $container[MostPopularElementsService::class] = function (ContainerInterface $container) {
            return new MostPopularElementsService(
                $container->get(CacheService::class)
            );
        };

        $container[MostPopularCoursesService::class] = function (ContainerInterface $container) {
            return new MostPopularCoursesService(
                $container->get('settings'),
                $container->get(CacheService::class)
            );
        };

        $container[UpdateDownloadsService::class] = function (ContainerInterface $container) {
            return new UpdateDownloadsService(
                $container->get(CacheService::class)
            );
        };

        $container[CourseListService::class] = function () {
            return new CourseListService();
        };

        $container[DownloadFileInfoService::class] = function (ContainerInterface $container) {
            return new DownloadFileInfoService(
                $container->get(UpdateDownloadsService::class)
            );
        };

        $container[UserService::class] = function (ContainerInterface $container) {
            return new UserService(
                $container->get(SessionService::class)
            );
        };

        $container[CourseMapper::class] = function (ContainerInterface $container) {
            return new CourseMapper(
                $container->get(UrlService::class)
            );
        };

        $container[ElementMapper::class] = function (ContainerInterface $container) {
            return new ElementMapper(
                $container->get(UrlService::class),
                $container->get(CourseMapper::class),
                $container->get(DownloadCountService::class)
            );
        };

        $container[UrlService::class] = function (ContainerInterface $container) {
            return new UrlService(
                $container->get('router')
            );
        };

        $container[ArchiveService::class] = function (ContainerInterface $container) {
            return new ArchiveService(
                $container->get(ElementMapper::class)
            );
        };

        $container[DownloadCountService::class] = function (ContainerInterface $container) {
            return new DownloadCountService(
                $container->get(CacheService::class)
            );
        };

        $container[JobService::class] = function (ContainerInterface $container) {
            return new JobService($container);
        };

        $container[RemoveOldSessionsJobServiceService::class] = function (ContainerInterface $container) {
            return new RemoveOldSessionsJobServiceService(
                $container->get(SessionService::class)
            );
        };

        $container[UpdateMostPopularElementsJobService::class] = function (ContainerInterface $container) {
            return new UpdateMostPopularElementsJobService(
                $container->get(MostPopularElementsService::class)
            );
        };

        $container[UpdateMostPopularCoursesJobService::class] = function (ContainerInterface $container) {
            return new UpdateMostPopularCoursesJobService(
                $container->get(MostPopularCoursesService::class)
            );
        };
    }
}

