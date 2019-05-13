<?php
namespace Youkok\Common\Containers;

use Psr\Container\ContainerInterface;

use Youkok\Biz\Services\ArchiveService;
use Youkok\Biz\Services\AutocompleteService;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\CourseListService;
use Youkok\Biz\Services\Download\DownloadCountService;
use Youkok\Biz\Services\Download\DownloadFileInfoService;
use Youkok\Biz\Services\FrontpageService;
use Youkok\Biz\Services\Job\Jobs\PopulateAutocompleteFileJobService;
use Youkok\Biz\Services\Job\Jobs\RemoveOldSessionsJobServiceJobService;
use Youkok\Biz\Services\Job\Jobs\UpdateMostPopularCoursesJobService;
use Youkok\Biz\Services\Job\Jobs\UpdateMostPopularElementsJobService;
use Youkok\Biz\Services\Job\JobService;
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
    public static function load(ContainerInterface $container): void
    {
        $container[SessionService::class] = function (): SessionService {
            return new SessionService();
        };

        $container[SearchRedirectService::class] = function (): SearchRedirectService {
            return new SearchRedirectService();
        };

        $container[CacheService::class] = function (ContainerInterface $container): CacheService {
            return new CacheService(
                $container->get('cache')
            );
        };

        $container[FrontpageService::class] = function (ContainerInterface $container): FrontpageService {
            return new FrontpageService(
                $container->get(SessionService::class),
                $container->get(MostPopularCoursesService::class),
                $container->get(MostPopularElementsService::class),
                $container->get(UserService::class)
            );
        };

        $container[MostPopularElementsService::class] = function (ContainerInterface $container): MostPopularElementsService {
            return new MostPopularElementsService(
                $container->get(CacheService::class)
            );
        };

        $container[MostPopularCoursesService::class] = function (ContainerInterface $container): MostPopularCoursesService {
            return new MostPopularCoursesService(
                $container->get('settings'),
                $container->get(CacheService::class)
            );
        };

        $container[UpdateDownloadsService::class] = function (ContainerInterface $container): UpdateDownloadsService {
            return new UpdateDownloadsService(
                $container->get(CacheService::class)
            );
        };

        $container[CourseListService::class] = function (): CourseListService {
            return new CourseListService();
        };

        $container[DownloadFileInfoService::class] = function (ContainerInterface $container): DownloadFileInfoService {
            return new DownloadFileInfoService(
                $container->get(UpdateDownloadsService::class)
            );
        };

        $container[UserService::class] = function (ContainerInterface $container): UserService {
            return new UserService(
                $container->get(SessionService::class)
            );
        };

        $container[CourseMapper::class] = function (ContainerInterface $container): CourseMapper {
            return new CourseMapper(
                $container->get(UrlService::class)
            );
        };

        $container[ElementMapper::class] = function (ContainerInterface $container): ElementMapper {
            return new ElementMapper(
                $container->get(UrlService::class),
                $container->get(CourseMapper::class),
                $container->get(DownloadCountService::class)
            );
        };

        $container[UrlService::class] = function (ContainerInterface $container): UrlService {
            return new UrlService(
                $container->get('router')
            );
        };

        $container[ArchiveService::class] = function (ContainerInterface $container): ArchiveService {
            return new ArchiveService(
                $container->get(ElementMapper::class)
            );
        };

        $container[DownloadCountService::class] = function (ContainerInterface $container): DownloadCountService {
            return new DownloadCountService(
                $container->get(CacheService::class)
            );
        };

        $container[JobService::class] = function (ContainerInterface $container): JobService {
            return new JobService($container);
        };

        $container[RemoveOldSessionsJobServiceJobService::class] = function (ContainerInterface $container): RemoveOldSessionsJobServiceJobService {
            return new RemoveOldSessionsJobServiceJobService(
                $container->get(SessionService::class)
            );
        };

        $container[UpdateMostPopularElementsJobService::class] = function (ContainerInterface $container): UpdateMostPopularElementsJobService {
            return new UpdateMostPopularElementsJobService(
                $container->get(MostPopularElementsService::class)
            );
        };

        $container[UpdateMostPopularCoursesJobService::class] = function (ContainerInterface $container): UpdateMostPopularCoursesJobService {
            return new UpdateMostPopularCoursesJobService(
                $container->get(MostPopularCoursesService::class)
            );
        };

        $container[PopulateAutocompleteFileJobService::class] = function (ContainerInterface $container): PopulateAutocompleteFileJobService {
            return new PopulateAutocompleteFileJobService(
                $container->get(AutocompleteService::class)
            );
        };

        $container[AutocompleteService::class] = function (ContainerInterface $container): AutocompleteService {
            return new AutocompleteService(
                $container->get(UrlService::class)
            );
        };
    }
}

