<?php
namespace Youkok2\Views;

use Youkok2\Models\Element;
use Youkok2\Models\Me;
use Youkok2\Utilities\Routes;
use Youkok2\Utilities\Loader;

class Archive extends BaseView
{
    
    public function __construct($app) {
        parent::__construct($app);
    }

    public function run() {
        $this->addSiteData('view', 'archive');
        $this->addSiteData('can_contribute', $this->me->canContribute());
        $this->addSiteData('file_types', explode(',', ACCEPTED_FILEENDINGS));
        
        $this->checkValidArchive();

        if ($this->getSetting('kill') !== true) {
            $this->template->assign('HEADER_MENU', 'ARCHIVE');

            $this->displayAndCleanup('archive.tpl', $this->path);
        }
    }
    
    private function checkValidArchive() {
        $element = Element::get($this->path);

        if ($element->wasFound() and $element->isDirectory()) {
            $this->setElementInformation($element);
        }
        else {
            $this->display404();
        }
    }
    
    private function setElementInformation($element) {
        $this->addSiteData('archive_id', $element->getId());

        $this->template->assign('ARCHIVE_ELEMENT', $element);

        $this->template->assign('ARCHIVE_ELEMENT_PARENTS', array_reverse($element->getParents()));
        
        $element_root = $element;
        if (!$element->hasParent()) {
            $element_root = $element->getRootParent();
        }
        $this->template->assign('ARCHIVE_ELEMENT_ROOT', $element_root);
        
        $this->updateLastVisited($element_root);
        
        $site_description  = $element_root->getCourseCode() . ' - ' . $element_root->getCourseName() . ': Øvinger, ';
        $site_description .= 'løsningsforslag, gamle eksamensoppgaver og andre ressurser på Youkok2.com, den beste ';
        $site_description .= 'kokeboka på nettet.';

        $this->template->assign('SITE_DESCRPTION', $site_description);
        
        if ($element->isEmpty()) {
            $this->template->assign('ARCHIVE_EMPTY', true);
        }
        else {
            $this->template->assign('ARCHIVE_EMPTY', false);
            $this->template->assign('ARCHIVE_CONTENT', $element->getChildren());
        }

        if ($element_root->getExam() !== null and strtotime($element_root->getExam()) > time()) {
            $this->template->assign('ARCHIVE_EXAM', true);
            $this->template->assign('ARCHIVE_EXAM_OBJECT', $element_root);
        }
        else {
            $this->template->assign('ARCHIVE_EXAM', false);
        }

        $alias_for = [];
        if (!$element->hasParent()) {
            $alias_ids = $element->getAliasFor();

            if (count($alias_ids) > 0) {
                foreach ($alias_ids as $alias_id) {
                    $element = Element::get($alias_id);
                    
                    $alias_for[] = $element;
                }
            }
        }
        
        $this->template->assign('ARCHIVE_ALIAS_FOR', $alias_for);
    }
    
    private function updateLastVisited($obj) {
        if ($obj != null and $obj->wasFound()) {
            $obj->updateLastVisited();
        }
    }
}
