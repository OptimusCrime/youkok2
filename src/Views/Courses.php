<?php
namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Models\Element;
use Youkok\Processors\CourseListProcessor;

class Courses extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        return $this->render($response, 'courses.html', [
            'SITE_TITLE' => 'Emner',
            'HEADER_MENU' => 'courses',
            'VIEW_NAME' => 'courses',
            'COURSES' => CourseListProcessor::get()
        ]);
    }
}
