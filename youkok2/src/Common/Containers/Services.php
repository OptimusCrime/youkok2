<?php
namespace Youkok\Common\Containers;

use Psr\Container\ContainerInterface;
use Monolog\Logger as MonoLogger;

use Youkok\Biz\Services\ArchiveHistoryService;
use Youkok\Biz\Services\ArchiveService;
use Youkok\Biz\Services\CoursesLookupService;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\CourseListService;
use Youkok\Biz\Services\Download\DownloadCountService;
use Youkok\Biz\Services\Download\DownloadFileInfoService;
use Youkok\Biz\Services\Admin\LoginService;
use Youkok\Biz\Services\FrontpageService;
use Youkok\Biz\Services\Job\Jobs\PopulateCoursesLookupFileJobService;
use Youkok\Biz\Services\Job\Jobs\RemoveOldSessionsJobServiceJobService;
use Youkok\Biz\Services\Job\Jobs\UpdateMostPopularCoursesJobService;
use Youkok\Biz\Services\Job\Jobs\UpdateMostPopularElementsJobService;
use Youkok\Biz\Services\Job\JobService;
use Youkok\Biz\Services\Mappers\CourseMapper;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Biz\Services\Models\CourseService;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Biz\Services\Models\SessionService;
use Youkok\Biz\Services\PopularListing\MostPopularCoursesService;
use Youkok\Biz\Services\PopularListing\MostPopularElementsService;
use Youkok\Biz\Services\UserSessionService;
use Youkok\Biz\Services\Download\UpdateDownloadsService;
use Youkok\Biz\Services\SystemLogService;
use Youkok\Biz\Services\UrlService;

class Services implements ContainersInterface
{
    public static function load(ContainerInterface $container): void
    {
        $container[UserSessionService::class] = function (ContainerInterface $container): UserSessionService {
            return new UserSessionService(
                $container->get(SessionService::class),
                $container->get(MonoLogger::class),
            );
        };

        $container[LoginService::class] = function (): LoginService {
            return new LoginService();
        };

        $container[CacheService::class] = function (ContainerInterface $container): CacheService {
            return new CacheService(
                $container->get('cache')
            );
        };

        $container[FrontpageService::class] = function (ContainerInterface $container): FrontpageService {
            return new FrontpageService(
                $container->get(UserSessionService::class),
                $container->get(MostPopularCoursesService::class),
                $container->get(MostPopularElementsService::class),
                $container->get(CacheService::class),
                $container->get(ElementService::class),
                $container->get(CourseService::class),
                $container->get(DownloadService::class),
            );
        };

        $container[MostPopularElementsService::class] = function (ContainerInterface $container): MostPopularElementsService {
            return new MostPopularElementsService(
                $container->get(CacheService::class),
                $container->get(DownloadService::class),
                $container->get(ElementService::class)
            );
        };

        $container[MostPopularCoursesService::class] = function (ContainerInterface $container): MostPopularCoursesService {
            return new MostPopularCoursesService(
                $container->get('settings'),
                $container->get(CacheService::class),
                $container->get(MonoLogger::class),
                $container->get(DownloadService::class),
                $container->get(ElementService::class)
            );
        };

        $container[UpdateDownloadsService::class] = function (ContainerInterface $container): UpdateDownloadsService {
            return new UpdateDownloadsService(
                $container->get(CacheService::class),
                $container->get(DownloadService::class)
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

        $container[CourseMapper::class] = function (ContainerInterface $container): CourseMapper {
            return new CourseMapper(
                $container->get(UrlService::class)
            );
        };

        $container[ElementMapper::class] = function (ContainerInterface $container): ElementMapper {
            return new ElementMapper(
                $container->get(UrlService::class),
                $container->get(CourseMapper::class),
                $container->get(DownloadCountService::class),
                $container->get(ElementService::class),
                $container->get(CourseService::class)
            );
        };

        $container[UrlService::class] = function (ContainerInterface $container): UrlService {
            return new UrlService(
                $container->get('router')
            );
        };

        $container[ArchiveService::class] = function (ContainerInterface $container): ArchiveService {
            return new ArchiveService(
                $container->get(ElementMapper::class),
                $container->get(ElementService::class),
                $container->get(CourseService::class)
            );
        };

        $container[ArchiveHistoryService::class] = function (ContainerInterface $container): ArchiveHistoryService {
            return new ArchiveHistoryService(
                $container->get(ElementMapper::class),
                $container->get(ElementService::class)
            );
        };

        $container[DownloadCountService::class] = function (ContainerInterface $container): DownloadCountService {
            return new DownloadCountService(
                $container->get(CacheService::class),
                $container->get(DownloadService::class)
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

        $container[PopulateCoursesLookupFileJobService::class] = function (ContainerInterface $container): PopulateCoursesLookupFileJobService {
            return new PopulateCoursesLookupFileJobService(
                $container->get(CoursesLookupService::class)
            );
        };

        $container[CoursesLookupService::class] = function (ContainerInterface $container): CoursesLookupService {
            return new CoursesLookupService(
                $container->get(UrlService::class),
                $container->get(CourseService::class)
            );
        };

        $container[SystemLogService::class] = function (): SystemLogService {
            return new SystemLogService();
        };

        $container[CourseService::class] = function (ContainerInterface $container): CourseService {
            return new CourseService(
                $container->get(ElementService::class)
            );
        };

        $container[DownloadService::class] = function (ContainerInterface $container): DownloadService {
            return new DownloadService(
                $container->get(ElementService::class)
            );
        };

        $container[ElementService::class] = function (ContainerInterface $container): ElementService {
            return new ElementService(
                $container->get(CacheService::class)
            );
        };

        $container[SessionService::class] = function (): SessionService {
            return new SessionService();
        };
    }
}
