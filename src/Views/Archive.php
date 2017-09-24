<?php
namespace Youkok\Views;

use \Carbon\Carbon;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Container;

use Youkok\Models\Element;

class Archive extends BaseView
{
    private $rootParent;
    private $parents;
    private $element;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->rootParent = null;
        $this->parents = null;
        $this->element = null;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        $uri = self::cleanUri($request->getAttribute('params'));

        if (!$this->getArchiveObject($uri)) {
            return $this->render404($response);
        }

        $this->setArchiveData();
        $this->updateRootParent();


        return $this->render($response, 'archive.tpl', [
            'HEADER_MENU' => 'courses',
            'VIEW_NAME' => 'archive'
        ]);
    }

    private function setArchiveData()
    {
        $this->setTemplateData('ARCHIVE_ID', $this->element->id);
        $this->setTemplateData('ARCHIVE_SUB_TITLE', null);
        $this->setTemplateData('ARCHIVE_PARENTS', $this->getArchiveParents());
        $this->setTemplateData('SITE_DESCRIPTION', $this->getSiteDescription());

        // Load empty
        $this->setTemplateData('ARCHIVE_CHILDREN', $this->getArchiveChildren());

        // Title
        if ($this->element->parent === null) {
            $this->setTemplateData('ARCHIVE_TITLE', $this->element->courseCode);
            $this->setTemplateData('ARCHIVE_SUB_TITLE', $this->element->courseName);
            $this->setTemplateData('SITE_TITLE', $this->element->courseCode . ' :: ' . $this->element->courseName);
        }
        else {
            $this->setTemplateData('ARCHIVE_TITLE', $this->element->name);
            $this->setTemplateData('SITE_TITLE', $this->element->name);
        }
    }

    private function updateRootParent()
    {
        /*
        $this->rootParent->last_visited = Carbon::now();
        $this->rootParent->save();
        */
    }

    private function getSiteDescription()
    {
        $descriptionObject = $this->element;
        if ($this->element->parent !== null) {
            // Safe guarding nullpointer
            if ($this->rootParent === null) {
                $this->getArchiveParents();
            }

            $descriptionObject = $this->rootParent;
        }

        return $descriptionObject->courseCode . ' - ' . $descriptionObject->courseName . ': ' .
            'Øvinger, løsningsforslag, gamle eksamensoppgaver ' .
            'og andre ressurser på Youkok2.com, den beste kokeboka på nettet.';
    }

    private function getArchiveChildren()
    {
        if ($this->element->empty) {
            return [];
        }

        $children = Element::select('id', 'name', 'slug', 'uri', 'parent', 'empty', 'directory', 'link', 'checksum')
            ->where('parent', $this->element->id)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->orderBy('directory', 'DESC')
            ->orderBy('name', 'ASC')
            ->get();

        // Guard for empty set of query
        if (count($children) > 0) {
            return $children;
        }

        return [];
    }

    private function getArchiveParents()
    {
        /*
        if ($this->this->parents !== null) {
            return $this->this->parents;
        }

        $parents = [$this->element];
        $currentParentId = $this->element->parent;
        do {
            $directParent = Element::select('id', 'name', 'slug', 'uri', 'parent')
                ->where('id', $currentParentId)
                ->where('deleted', 0)
                ->where('pending', 0)
                ->where('directory', 1)
                ->first();

            if ($directParent === null) {
                break;
            }

            $parents[] = $directParent;
            $currentParentId = $directParent->parent;

        } while ($currentParentId !== 0 and $currentParentId !== null);

        $this->parents = array_reverse($parents);

        $this->rootParent = $this->parents[0];

        return $this->parents;
        */
    }

    private function getArchiveObject($uri)
    {
        if ($this->getObjectByUri($uri)) {
            return true;
        }

        return $this->getObjectByFragments($uri);
    }

    private function getObjectByUri($uri)
    {
        $element = Element::select('id', 'parent', 'name', 'checksum', 'link')
            ->where('uri', $uri)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->where('directory', 1)
            ->first();

        if ($element->checksum !== null or $element->link !== null) {
            return false;
        }

        $this->element = $element;

        return true;
    }

    private function getObjectByFragments($uri)
    {
        // TODO
        return false;
    }

    private static function cleanUri($uri)
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
