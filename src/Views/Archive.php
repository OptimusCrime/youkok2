<?php
namespace Youkok\Views;

use \Carbon\Carbon;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Container;

use Youkok\Models\Element;
use Youkok\Processors\ArchiveElementFetchProcessor;
use Youkok\Processors\ArchiveVisitProcessor;

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

        ArchiveVisitProcessor::fromElement($element)
            ->withSessionHandler($this->sessionHandler)
            ->run();

        $this->setSiteData('archive_id', $element->id);
        $this->setSiteData('file_types', $this->container->get('settings')['file_endings']);

        return $this->render($response, 'archive.html', [
            'HEADER_MENU' => 'courses',
            'VIEW_NAME' => 'archive',
            'ARCHIVE' => ArchiveElementFetchProcessor
                ::fromElement($element)
                ->withSessionHandler($this->sessionHandler)
                ->withSettings($this->container->get('settings'))
                ->withCache($this->container->get('cache'))
                ->run(),
            'BODY_CLASS' => 'archive'
        ]);
    }
}
