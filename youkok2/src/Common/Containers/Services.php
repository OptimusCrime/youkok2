<?php

namespace Youkok\Common\Containers;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;

use Youkok\Biz\Services\Admin\CacheContentService;
use Youkok\Biz\Services\Admin\FileCreateDirectoryService;
use Youkok\Biz\Services\Admin\FilesService;
use Youkok\Biz\Services\Admin\FileDetailsService;
use Youkok\Biz\Services\Admin\FileUpdateService;
use Youkok\Biz\Services\Admin\HomeGraphService;
use Youkok\Biz\Services\Admin\FileListingService;
use Youkok\Biz\Services\ArchiveHistoryService;
use Youkok\Biz\Services\ArchiveService;
use Youkok\Biz\Services\Auth\AuthService;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\CoursesLookupService;
use Youkok\Biz\Services\Download\DownloadFileInfoService;
use Youkok\Biz\Services\FrontpageService;
use Youkok\Biz\Services\Job\Jobs\UpdateDownloadsJobService;
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
use Youkok\Biz\Services\UrlService;

class Services
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function load(Container $container): void
    {
        $container->set(AuthService::class, new AuthService());

        $container->set(CacheService::class,
            new CacheService(
                $container->get('cache')
            ));

        $container->set(ElementService::class,
            new ElementService(
                $container->get(CacheService::class)
            )
        );

        $container->set(UrlService::class,
            new UrlService(
                $container->get(ElementService::class)
            )
        );

        $container->set(DownloadService::class,
            new DownloadService(
                $container->get(ElementService::class)
            )
        );

        $container->set(CourseMapper::class,
            new CourseMapper(
                $container->get(UrlService::class)
            )
        );

        $container->set(ElementMapper::class,
            new ElementMapper(
                $container->get(UrlService::class),
                $container->get(CourseMapper::class),
                $container->get(ElementService::class),
                $container->get('logger')
            )
        );

        $container->set(CourseService::class,
            new CourseService(
                $container->get(CacheService::class),
            )
        );

        $container->set(FrontpageService::class,
            new FrontpageService(
                $container->get(CacheService::class),
                $container->get(ElementService::class),
                $container->get(CourseService::class),
                $container->get(DownloadService::class),
                $container->get(ElementMapper::class),
                $container->get(CourseMapper::class),
            )
        );

        $container->set(MostPopularElementsService::class,
            new MostPopularElementsService(
                $container->get(CacheService::class),
                $container->get(DownloadService::class),
                $container->get(ElementService::class),
                $container->get(ElementMapper::class),
                $container->get('logger')
            )
        );

        $container->set(MostPopularCoursesService::class,
            new MostPopularCoursesService(
                $container->get(CacheService::class),
                $container->get(DownloadService::class),
                $container->get(ElementService::class),
                $container->get(CourseMapper::class),
                $container->get('logger')
            )
        );

        $container->set(UpdateDownloadsService::class,
            new UpdateDownloadsService(
                $container->get(CacheService::class),
                $container->get(DownloadService::class)
            )
        );

        $container->set(DownloadFileInfoService::class,
            new DownloadFileInfoService()
        );

        $container->set(CoursesLookupService::class,
            new CoursesLookupService(
                $container->get(CourseService::class),
                $container->get(CourseMapper::class),
                $container->get(CacheService::class),
            )
        );

        $container->set(ArchiveService::class,
            new ArchiveService(
                $container->get(ElementMapper::class),
                $container->get(ElementService::class),
            )
        );

        $container->set(ArchiveHistoryService::class,
            new ArchiveHistoryService(
                $container->get(ElementMapper::class),
                $container->get(ElementService::class)
            )
        );

        $container->set(JobService::class,
            new JobService($container, $container->get('logger'))
        );

        $container->set(UpdateDownloadsJobService::class,
            new UpdateDownloadsJobService(
                $container->get(CacheService::class),
                $container->get(ElementService::class),
            )
        );

        $container->set(TitleFetchService::class,
            new TitleFetchService()
        );

        $container->set(CreateLinkService::class,
            new CreateLinkService(
                $container->get(ElementService::class)
            )
        );

        $container->set(CreateFileService::class,
            new CreateFileService(
                $container->get(ElementService::class)
            )
        );

        $container->set(HomeGraphService::class,
            new HomeGraphService(
                $container->get(AdminDownloadService::class)
            )
        );

        $container->set(FilesService::class,
            new FilesService(
                $container->get(ElementService::class),
                $container->get(AdminElementMapper::class),
            )
        );

        $container->set(AdminCourseService::class,
            new AdminCourseService()
        );

        $container->set(FileListingService::class,
            new FileListingService(
                $container->get(AdminCourseService::class),
                $container->get(FilesService::class)
            )
        );

        $container->set(FileDetailsService::class,
            new FileDetailsService(
                $container->get(AdminCourseService::class),
                $container->get(ElementService::class),
                $container->get(DownloadFileInfoService::class),
            )
        );

        $container->set(FileUpdateService::class,
            new FileUpdateService(
                $container->get(FilesService::class),
                $container->get(AdminElementService::class),
                $container->get(ElementService::class),
                $container->get(CacheService::class),
            )
        );

        $container->set(FileCreateDirectoryService::class,
            new FileCreateDirectoryService(
                $container->get(FilesService::class),
                $container->get(ElementService::class),
            )
        );

        $container->set(CacheContentService::class,
            new CacheContentService(
                $container->get(CacheService::class)
            )
        );

        $container->set(AdminElementMapper::class,
            new AdminElementMapper(
                $container->get(ElementMapper::class),
                $container->get(UrlService::class),
            )
        );

        $container->set(AdminElementService::class,
            new AdminElementService()
        );

        $container->set(AdminDownloadService::class,
            new AdminDownloadService()
        );

    }
}
