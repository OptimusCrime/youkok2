<?php
declare(strict_types=1);

namespace Youkok\Views;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Models\Element;

class Archive extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args): Response
    {
        $uri = self::cleanUri($request->getAttribute('params'));

        $archiveObject = $this->getArchiveObject($uri);
        if ($archiveObject === null) {
            return $this->render404($response);
        }

        $this->setArchiveData($archiveObject);

        return $this->render($response, 'archive.tpl', [
            'HEADER_MENU' => 'courses',
            'VIEW_NAME' => 'archive'
        ]);
    }

    private function setArchiveData(Element $element)
    {
        $this->setTemplateData('ARCHIVE_ID', $element->id);
        $this->setTemplateData('ARCHIVE_SUB_TITLE', null);
        $this->setTemplateData('ARCHIVE_EMPTY', true);
        $this->setTemplateData('ARCHIVE_PARENTS', self::getArchiveParents($element));

        if ($element->parent === null) {
            $this->setTemplateData('ARCHIVE_TITLE', $element->courseCode);
            $this->setTemplateData('ARCHIVE_SUB_TITLE', $element->courseName);
        }
        else {
            $this->setTemplateData('ARCHIVE_TITLE', $element->name);
        }
    }

    private static function getArchiveParents(Element $element): array
    {
        $parents = [$element];
        $currentParentId = $element->parent;
        do {
            $directParent = Element::select('id', 'name', 'slug', 'uri', 'parent')
                ->where('id', $currentParentId)
                ->first();

            if ($directParent === null) {
                break;
            }

            $parents[] = $directParent;
            $currentParentId = $directParent->parent;

        } while ($currentParentId !== 0 and $currentParentId !== null);

        return array_reverse($parents);
    }

    private function getArchiveObject(string $uri)
    {
        $uriObject = $this->getObjectByUri($uri);
        if ($uriObject !== null) {
            return $uriObject;
        }

        return $this->getObjectByFragments($uri);
    }

    private function getObjectByUri(string $uri)
    {
        $element = Element::select('id', 'parent', 'name', 'checksum', 'link')
            ->where('uri', $uri)
            ->first();

        if ($element->checksum !== null or $element->link !== null) {
            return null;
        }

        return $element;
    }

    private function getObjectByFragments(string $uri)
    {
        // TODO
    }

    private static function cleanUri(string $uri): string
    {
        $fragments = [];
        $uriSplit = explode('/', $uri);
        foreach ($uriSplit as $item) {
            if (strlen($item) > 0) {
                $fragments[] = $item;
            }
        }

        return implode('/', $fragments);
    }
}
