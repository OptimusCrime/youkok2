<?php

namespace Youkok\Common\Containers;

use Psr\Container\ContainerInterface;

use Youkok\Biz\Services\ArchiveService;
use Youkok\Biz\Services\Cache\CacheService;
use Youkok\Biz\Services\Course\CourseService;
use Youkok\Biz\Services\CourseListService;
use Youkok\Biz\Services\Download\DownloadService;
use Youkok\Biz\Services\Element\ElementService;
use Youkok\Biz\Services\FrontpageService;
use Youkok\Biz\Services\Mappers\CourseMapper;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Biz\Services\PopularListing\PopularCoursesService;
use Youkok\Biz\Services\PopularListing\PopularElementsService;
use Youkok\Biz\Services\SearchRedirectService;
use Youkok\Biz\Services\SessionService;
use Youkok\Biz\Services\Download\UpdateDownloadsService;
use Youkok\Biz\Services\Cache\UpdateMostPopularElementRedisService;
use Youkok\Biz\Services\UrlService;
use Youkok\Biz\Services\User\UserService;
use Youkok\CachePopulators\PopulateMostPopularElements;

class Services implements ContainersInterface
{
    public static function load(ContainerInterface $container)
    {
        $container[SessionService::class] = function () {
            return new SessionService();
        };

        $container[CacheService::class] = function (ContainerInterface $container) {
            return new CacheService(
                $container->get('cache'),
                $container->get(PopulateMostPopularElements::class)
            );
        };

        $container[FrontpageService::class] = function (ContainerInterface $container) {
            return new FrontpageService(
                $container->get(SessionService::class),
                $container->get('cache'),
                $container->get(PopularCoursesService::class),
                $container->get(PopularElementsService::class),
                $container->get(ElementService::class),
                $container->get(DownloadService::class),
                $container->get(CourseService::class),
                $container->get(UserService::class)
            );
        };

        $container[PopularElementsService::class] = function (ContainerInterface $container) {
            return new PopularElementsService(
                $container->get(SessionService::class),
                $container->get('cache'),
                $container->get(CacheService::class)
            );
        };

        $container[PopularCoursesService::class] = function (ContainerInterface $container) {
            return new PopularCoursesService(
                $container->get(SessionService::class),
                $container->get('cache'),
                $container->get(CacheService::class)
            );
        };

        $container[SearchRedirectService::class] = function (ContainerInterface $container) {
            return new SearchRedirectService(
                $container->get('response'),
                $container->get('router')
            );
        };

        $container[UpdateDownloadsService::class] = function (ContainerInterface $container) {
            return new UpdateDownloadsService(
                $container->get(SessionService::class),
                $container->get('cache'),
                $container->get(UpdateMostPopularElementRedisService::class)
            );
        };

        $container[UpdateMostPopularElementRedisService::class] = function (ContainerInterface $container) {
            return new UpdateMostPopularElementRedisService(
                $container->get('cache')
            );
        };

        $container[PopulateMostPopularElements::class] = function (ContainerInterface $container) {
            return new PopulateMostPopularElements(
                $container->get('cache')
            );
        };

        $container[CourseListService::class] = function () {
            return new CourseListService();
        };

        $container[DownloadService::class] = function (ContainerInterface $container) {
            return new DownloadService(
                $container->get(UpdateDownloadsService::class)
            );
        };

        $container[ElementService::class] = function (ContainerInterface $container) {
            return new ElementService(
                $container->get(CacheService::class)
            );
        };

        $container[CourseService::class] = function () {
            return new CourseService();
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
                $container->get(ElementService::class),
                $container->get(CourseService::class),
                $container->get(CourseMapper::class)
            );
        };

        $container[UrlService::class] = function (ContainerInterface $container) {
            return new UrlService(
                $container->get('router')
            );
        };

        $container[ArchiveService::class] = function (ContainerInterface $container) {
            return new ArchiveService();
        };
    }
}

