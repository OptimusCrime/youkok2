<?php
namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Container;

use Youkok\Controllers\ElementController;
use Youkok\Helpers\Utilities;
use Youkok\Models\Contributor;
use Youkok\Models\Download;
use Youkok\Models\Element;
use Youkok\Processors\PopularCoursesProcessor;
use Youkok\Processors\PopularElementsProcessor;

class Frontpage extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        return $this->render($response, 'frontpage.tpl', [
            'FRONTPAGE_INFO_USERS' => Utilities::numberFormat(Contributor::where('banned', 0)->count()),
            'FRONTPAGE_INFO_FILES' => Utilities::numberFormat(Element::where('directory', 0)->where('deleted', 0)->count()),
            'FRONTPAGE_INFO_DOWNLOADS' => Utilities::numberFormat(Download::count()),
            'FRONTPAGE_LATEST_ELEMENTS' => ElementController::getLatest(),
            'FRONTPAGE_MOST_POPULAR_ELEMENTS' => PopularElementsProcessor::currentUser(),
            'FRONTPAGE_MOST_POPULAR_COURSES' => PopularCoursesProcessor::currentUser(),
            'FRONTPAGE_LATEST_VISITED' => ElementController::getLastVisitedCourses()
        ]);
    }
}
