<?php
namespace Youkok\Views;

use \Carbon\Carbon;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Container;

use Youkok\Models\Element;
use Youkok\Processors\ArchiveElementFetchProcessor;

class Archive extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        if (empty($args) or !isset($args['params']) or strlen($args['params']) === 0) {
            $courseView = new Courses($this->container);
            return $courseView->view($request, $response, $args);
        }


        $element = Element::fromUri($request->getAttribute('params'));
        if ($element === null) {
            return $this->render404($response);
        }

        $element->updateRootParent();

        return $this->render($response, 'archive.html', [
            'HEADER_MENU' => 'courses',
            'VIEW_NAME' => 'archive',
            'ARCHIVE' => ArchiveElementFetchProcessor::fromElement($element),
            'BODY_CLASS' => 'archive'
        ]);
    }
}
