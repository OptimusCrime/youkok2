<?php
namespace Youkok\Web\Views\Admin;

use Slim\Http\Response;
use Slim\Http\Request;
use Youkok\Common\Utilities\CoursesCacheConstants;

class AdminFiles extends AdminBaseView
{
    public function view(Request $request, Response $response): Response
    {
        $this->setSiteData('view', 'admin_files');

        return $this->renderAdminFiles($response);
    }

    public function viewOne(Request $request, Response $response, array $args): Response
    {
        $this->setSiteData('view', 'admin_files');
        $this->setSiteData('admin_file', (int) $args['id']);

        return $this->renderAdminFiles($response);
    }

    private function renderAdminFiles(Response $response): Response
    {
        $this->overrideTemplateData('COURSE_LOOKUP', static::getAdminTemplateData());

        return $this->render($response, 'admin/files.html', [
            'SITE_TITLE' => 'Admin',
            'ADMIN_TITLE' => 'Filer',
            'HEADER_MENU' => 'admin_files',
            'VIEW_NAME' => 'admin_files',
            'BODY_CLASS' => 'admin'
        ]);
    }

    // TODO remove this
    private static function getAdminTemplateData()
    {
        $course_lookup = @file_get_contents(
            '/volume_data/cache/'
            . CoursesCacheConstants::DYNAMIC_SUB_DIRECTORY
            .  CoursesCacheConstants::ADMIN_CACHE_BUSTING_FILE_NAME
        );

        if ($course_lookup === false) {
            return '';
        }

        return $course_lookup;
    }
}
