<?php
namespace Youkok\Views;

use \Carbon\Carbon;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Container;

use Youkok\Models\Element;

class Archive extends BaseView
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(Request $request, Response $response, array $args)
    {
        $element = Element::fromUri($request->getAttribute('params'));
        if ($element === null) {
            return $this->render404($response);
        }

        $element->updateRootParent();

        return $this->render($response, 'archive.html', [
            'HEADER_MENU' => 'courses',
            'VIEW_NAME' => 'archive',
            'ARCHIVE_ID' => $element->id,
            'ARCHIVE_PARENTS' => $element->parents,
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
}
