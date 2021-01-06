<?php
namespace Youkok\Common\Containers;

use Psr\Container\ContainerInterface;
use Monolog\Logger as MonoLogger;

use Youkok\Biz\Services\Admin\CacheContentService;
use Youkok\Biz\Services\Admin\FileCreateDirectoryService;
use Youkok\Biz\Services\Admin\FilesService;
use Youkok\Biz\Services\Admin\FileDetailsService;
use Youkok\Biz\Services\Admin\FileUpdateService;
use Youkok\Biz\Services\Admin\HomeGraphService;
use Youkok\Biz\Services\Admin\FileListingService;
use Youkok\Biz\Services\ArchiveHistoryService;
use Youkok\Biz\Services\ArchiveService;
use Youkok\Biz\Services\CoursesLookupService;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Download\DownloadCountService;
use Youkok\Biz\Services\Download\DownloadFileInfoService;
use Youkok\Biz\Services\FrontpageService;
use Youkok\Biz\Services\Job\Jobs\ClearReddisCachePartitionsService;
use Youkok\Biz\Services\Job\Jobs\PopulateCoursesLookupFileJobService;
use Youkok\Biz\Services\Job\Jobs\UpdateMostPopularCoursesJobService;
use Youkok\Biz\Services\Job\Jobs\UpdateMostPopularElementsJobService;
use Youkok\Biz\Services\Job\JobService;
use Youkok\Biz\Services\Mappers\Admin\AdminElementMapper;
use Youkok\Biz\Services\Mappers\CourseMapper;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Biz\Services\Models\Admin\AdminCourseService;
use Youkok\Biz\Services\Models\Admin\AdminDownloadService;
use Youkok\Biz\Services\Models\Admin\AdminElementService;
use Youkok\Biz\Services\Models\CourseService;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Biz\Services\PopularListing\MostPopularCoursesService;
use Youkok\Biz\Services\PopularListing\MostPopularElementsService;
use Youkok\Biz\Services\Post\Create\CreateFileService;
use Youkok\Biz\Services\Post\Create\CreateLinkService;
use Youkok\Biz\Services\Post\TitleFetchService;
use Youkok\Biz\Services\Download\UpdateDownloadsService;
use Youkok\Biz\Services\SystemLogService;
use Youkok\Biz\Services\UrlService;

class Services implements ContainersInterface
{
    public static function load(ContainerInterface $container): void
    {
        $container[CacheService::class] = function (ContainerInterface $container): CacheService {
            return new CacheService(
                $container->get('cache')
            );
        };

        $container[FrontpageService::class] = function (ContainerInterface $container): FrontpageService {
            return new FrontpageService(
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
                $container->get(ElementService::class),
                $container->get(MonoLogger::class)
            );
        };

        $container[MostPopularCoursesService::class] = function (ContainerInterface $container): MostPopularCoursesService {
            return new MostPopularCoursesService(
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
                $container->get(CourseService::class),
                $container->get(MonoLogger::class)
            );
        };

        $container[UrlService::class] = function (ContainerInterface $container): UrlService {
            return new UrlService(
                $container->get('router'),
                $container->get(ElementService::class)
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

        $container[ClearReddisCachePartitionsService::class] = function (ContainerInterface $container): ClearReddisCachePartitionsService {
            return new ClearReddisCachePartitionsService(
                $container->get(CacheService::class)
            );
        };

        $container[CoursesLookupService::class] = function (ContainerInterface $container): CoursesLookupService {
            return new CoursesLookupService(
                $container->get(UrlService::class),
                $container->get(CourseService::class),
                $container->get(MonoLogger::class)
            );
        };

        $container[SystemLogService::class] = function (): SystemLogService {
            return new SystemLogService();
        };

        $container[CourseService::class] = function (ContainerInterface $container): CourseService {
            return new CourseService(
                $container->get(ElementService::class),
                $container->get(CacheService::class),
                $container->get(UrlService::class)
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

        $container[TitleFetchService::class] = function (): TitleFetchService {
            return new TitleFetchService();
        };

        $container[CreateLinkService::class] = function (ContainerInterface $container): CreateLinkService {
            return new CreateLinkService(
                $container->get(ElementService::class)
            );
        };

        $container[CreateFileService::class] = function (ContainerInterface $container): CreateFileService {
            return new CreateFileService(
                $container->get(ElementService::class)
            );
        };

        $container[HomeGraphService::class] = function (ContainerInterface $container): HomeGraphService {
            return new HomeGraphService(
                $container->get(AdminDownloadService::class)
            );
        };

        $container[FileListingService::class] = function (ContainerInterface $container): FileListingService {
            return new FileListingService(
                $container->get(AdminCourseService::class),
                $container->get(FilesService::class)
            );
        };

        $container[FileDetailsService::class] = function (ContainerInterface $container): FileDetailsService {
            return new FileDetailsService(
                $container->get(AdminCourseService::class),
                $container->get(FilesService::class),
                $container->get(ElementService::class),
                $container->get(DownloadFileInfoService::class),
            );
        };

        $container[FileUpdateService::class] = function (ContainerInterface $container): FileUpdateService {
            return new FileUpdateService(
                $container->get(FilesService::class),
                $container->get(AdminElementService::class),
                $container->get(ElementService::class),
                $container->get(CacheService::class),
            );
        };

        $container[FileCreateDirectoryService::class] = function (ContainerInterface $container): FileCreateDirectoryService {
            return new FileCreateDirectoryService(
                $container->get(FilesService::class),
                $container->get(ElementService::class),
            );
        };

        $container[CacheContentService::class] = function (ContainerInterface $container): CacheContentService {
            return new CacheContentService(
                $container->get(CacheService::class)
            );
        };

        $container[FilesService::class] = function (ContainerInterface $container): FilesService {
            return new FilesService(
                $container->get(ElementService::class),
                $container->get(AdminElementMapper::class),
            );
        };

        $container[AdminElementMapper::class] = function (ContainerInterface $container): AdminElementMapper {
            return new AdminElementMapper(
                $container->get(ElementMapper::class),
                $container->get(UrlService::class),
            );
        };

        $container[AdminElementService::class] = function (ContainerInterface $container): AdminElementService {
            return new AdminElementService(
            );
        };

        $container[AdminDownloadService::class] = function (ContainerInterface $container): AdminDownloadService {
            return new AdminDownloadService(
            );
        };

        $container[AdminCourseService::class] = function (ContainerInterface $container): AdminCourseService {
            return new AdminCourseService(
            );
        };
    }
}
