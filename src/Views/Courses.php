<?php
declare(strict_types=1);

namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Models\Element;

class Courses extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args): Response
    {
        if (!$this->container->get('view')->getSmarty()->isCached('courses.tpl', $request->getUri()->getPath())) {
            $this->loadCourses();
        }

        return $this->render($response, 'courses.tpl', [
            'HEADER_MENU' => 'courses',
            'VIEW_NAME' => 'courses'
        ]);
    }

    private function loadCourses()
    {
        $collectionSize = 0;
        $previous_letter = null;
        $collection = [];

        $elementCollection = Element::select('id', 'name', 'slug', 'empty')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('pending', 0)
            ->orderBy('name')
            ->get();

        foreach ($elementCollection as $element) {
            $current_letter = substr($element->courseCode, 0, 1);

            if ($previous_letter != $current_letter) {
                $previous_letter = $current_letter;

                $collection[] = [
                    'letter' => $current_letter,
                    'courses' => []
                ];

                $collectionSize++;
            }
            $collection[$collectionSize - 1]['courses'][] = $element;
        }

        $this->setTemplateData('COURSE_COLLECTION', $collection);
    }
}
