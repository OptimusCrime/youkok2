<?php
/*
 * File: Archive.php
 * Holds: The complex and horrible archive view
 * Created: 02.10.2013
 * Project: Youkok2
 * 
 */

namespace Youkok2\Views;

use Youkok2\Models\Element;
use Youkok2\Models\Me;
use Youkok2\Utilities\Routes;
use Youkok2\Utilities\Loader;

class Archive extends BaseView {
    
    /*
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    /*
     * Run the view
     */

    public function run() {
        
        // Set information to site data
        $this->addSiteData('view', 'archive');
        $this->addSiteData('can_contribute', Me::canContribute());
        $this->addSiteData('file_types', explode(',', ACCEPTED_FILEENDINGS));
        
        // Load the archive
        $this->checkValidArchive();
        
        // Check if we should keep this view
        if ($this->getSetting('kill') !== true) {
            // Set stuff
            $this->template->assign('HEADER_MENU', 'ARCHIVE');

            // Display
            $this->displayAndCleanup('archive.tpl', $this->path);
        }
    }
    
    /*
     * Check if archive is valid or not
     */
    
    private function checkValidArchive() {
        // Try to create new element
        $element = Element::get($this->path);

        // Check if element was found and is directory
        if ($element->wasFound() and $element->isDirectory()) {
            // Set information related to the Element
            $this->setElementInformation($element);
        }
        else {
            $this->display404();
        }
    }
    
    /*
     * Set information related to the Element we are browsing
     */
    
    private function setElementInformation($element) {
        // Set id
        $this->addSiteData('archive_id', $element->getId());

        // Add element to Smarty
        $this->template->assign('ARCHIVE_ELEMENT', $element);

        // Get breadcrumbs
        $this->template->assign('ARCHIVE_ELEMENT_PARENTS', array_reverse($element->getParents()));
        
        // Set root element
        $element_root = $element;
        if (!$element->isCourse()) {
            $element_root = $element->getRootParent();
        }
        $this->template->assign('ARCHIVE_ELEMENT_ROOT', $element_root);
        
        // Update last visited
        $this->updateLastVisited($element_root);
        
        // Set the site description
        $site_description = $element_root->getCourseCode() . ' - ' . $element_root->getCourseName() . ': Øvinger, løsningsforslag, gamle eksamensoppgaver og andre ressurser på Youkok2.com, den beste kokeboka på nettet.';
        $this->template->assign('SITE_DESCRPTION', $site_description);
        
        // Check if archive is empty
        if ($element->isEmpty()) {
            $this->template->assign('ARCHIVE_EMPTY', true);
        }
        else {
            $this->template->assign('ARCHIVE_EMPTY', false);
            $this->template->assign('ARCHIVE_CONTENT', $element->getChildren());
        }
        
        // Check for exam
        if ($element_root->getExam() !== null and strtotime($element_root->getExam()) > time()) {
            $this->template->assign('ARCHIVE_EXAM', true);
            $this->template->assign('ARCHIVE_EXAM_OBJECT', $element_root);
        }
        else {
            $this->template->assign('ARCHIVE_EXAM', false);
        }
        
        // Attempt to fetch the aliases for this element
        $alias_for = [];
        if ($element->isCourse()) {
            $alias_ids = $element->getAliasFor();
            
            // If any aliases were found, loop the list and create the objects
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
    
    private function loadBredcrumbsTitle($element) {
        $ret = '';
        $arr = $element->getBreadcrumbs();
        foreach ($arr as $k => $v) {
            if ($k > 0) {
                if ($k == 1) {
                    $ret .= $v->getName() . ' :: ';
                }
                else {
                    $ret .= $v->getName() . ' / ';
                }
            }
        }
        
        return substr($ret, 0, strlen($ret) - 3);
    }
}