<?php
namespace Youkok\Web\Views;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Helpers\ElementHelper;
use Youkok\Common\Models\Element;
use Youkok\Biz\ArchiveElementFetchProcessor;
use Youkok\Biz\ArchiveVisitProcessor;

class Archive extends BaseView
{
    public function view(Request $request, Response $response, array $args)
    {
        die('nope');
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
            ->withSessionHandler($this->sessionService)
            ->run();

        $this->setSiteData('archive_id', $element->id);
        $this->setSiteData('file_types', $this->settings['file_endings']);

        return $this->render($response, 'archive.html', [
            'HEADER_MENU' => 'courses',
            'VIEW_NAME' => 'archive',
            'ARCHIVE' => ArchiveElementFetchProcessor
                ::fromElement($element)
                ->withSessionHandler($this->sessionService)
                ->withSettings($this->settings)
                ->withCache($this->cache)
                ->run(),
            'BODY_CLASS' => 'archive',
            'SITE_TITLE' => ElementHelper::siteTitleFor($element),
            'SITE_DESCRIPTION' => ElementHelper::siteDescriptionFor($element)
        ]);
    }
}
