<?php
/*
 * File: Archive.php
 * Holds: The complex and horrible archive view
 * Created: 02.10.2013
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

/*
 * Define what classes to use
 */

use \Youkok2\Models\Element as Element;
use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Routes as Routes;
use \Youkok2\Utilities\Utilities as Utilities;


/*
 * The Archive class, extending BaseView
 */

class Archive extends BaseView {

    /*
     * Constructor
     */

    public function __construct() {
        parent::__construct();
        
        // Set information to site data
        $this->addSiteData('view', 'archive');
        $this->addSiteData('can_contribute', Me::canContribute());
        $this->addSiteData('file_types', explode(',', ACCEPTED_FILEENDINGS));
        
        // Load the archive
        $this->checkValidArchive();

        // Set stuff
        $this->template->assign('HEADER_MENU', 'ARCHIVE');
        $this->template->assign('ARCHIVE_PATH', Routes::ARCHIVE);

        // Display
        $this->displayAndCleanup('archive.tpl', $this->queryGetClean());
    }
    
    /*
     * Check if archive is valid or not
     */
    
    private function checkValidArchive() {
        // Try to create new element
        $element = Element::get($this->queryGetClean());

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
        
        // Metadata
        if (!$element->isCourse()) {
            $element_root = $element->getRootParent();
            $course = $element_root->getCourse();
        }
        
        $site_description = $course['code'] . ' - ' . $course['name'] . ': Øvinger, løsningsforslag, gamle eksamensoppgaver og andre ressurser på Youkok2.com, den beste kokeboka på nettet.';
        $this->template->assign('SITE_DESCRPTION', $site_description);
        

        
        // Check if archive is empty
        if ($element->isEmpty()) {
            $this->template->assign('ARCHIVE_EMPTY', true);
        }
        else {
            $this->template->assign('ARCHIVE_EMPTY', false);
            $this->template->assign('ARCHIVE_CONTENT', $this->getElementContent($element));
        }
    }

    //
    // Method for loading breadcrumbs
    //

    private function loadBreadcrumbs($element) {
        // Some variables
        $ret = array();
        $collection = $element->getParents();
        
        // Loop and build list
        foreach ($collection as $v) {
            // Check if si course
            if ($v->isCourse()) {
                $course = $v->getCourse();
                $name = $course['code'];
            }
            else {
                $name = $v->getName();
            }
            
            // Find out what kind of element to use
            if ($v->getId() == $element->getId()) {
                // Current element, no link
                $ret[] = '<li class="active">' . $name . '</li>';
            }
            else {
                // Not current element, add link
                $ret[] = '<li><a href="' . $v->generateUrl(Routes::ARCHIVE) . '">' . $name . '</a></li>';
            }
            
        }

        // Return breadcrumbs
        return implode('', $ret);
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

    //
    // Method for loading the elements for this archive
    //

    private function getElementContent($element) {
        $ret = '';
        
        // Get children
        $children = $element->getChildren();
        
        // Loop children
        foreach ($children as $child) {
            $data = array();
            $url_target = '';
            
            // Some stuff
            if ($child->isDirectory()) {
                // Directory
                $title = $child->getName();
                $url = $child->generateUrl(Routes::ARCHIVE);
                $image = 'folder.png';
                $type = 'dir';
            }
            else if ($child->isLink()) {
                // Link
                $title = 'Link til: ' . $child->getUrl();
                $url = $child->generateUrl(Routes::REDIRECT);
                $url_target = ' target="_blank"';
                $image = 'link.png';
                $type = 'link';
            }
            else {
                // Normal file
                $title = $child->getName();
                $url = $child->generateUrl(Routes::DOWNLOAD);
                $url_target = ' target="_blank"';
                $image = (($child->getMissingImage() == 1) ? 'unknown' : $child->getMimeType()) . '.png';
                $type = 'file';
                $data[] = 'data-size="' . $child->getSize() . '"';
            }
            
            // Flag
            $div_flag = '';
            if ($child->getFlagCount() > 0) {
                $div_flag = '<div class="archive-badge">' . $child->controller->getFlagCount() . '</div>';
            }
            
            // Overlay
            $overlay = '';
            if (!$child->isAccepted()) {
                $overlay = ' class="archive-item-pending"';
            }
            
            // Collect data
            $data[] = 'data-id="' . $child->getId() . '"';
            $data[] = 'data-type="' . $type . '"';
            $data[] = 'data-name="' . $child->getName() . '"';
            $data[] = 'data-flags="' . $child->getFlagCount() . '"';
            $data[] = 'data-favorite="' . (int) Me::isFavorite($child->getId()) . '"';
            
            $dropdown  = '';
            $dropdown .= '<ul>';
            //$dropdown .= '    <li><a href="#">Info</a></li>';
            //$dropdown .= '    <li class="sep"></li>';
            //$dropdown .= '    <li><a href="#">Flagg</a></li>';
            //$dropdown .= '    <li><a href="#">Rapporter</a></li>';
            //$dropdown .= '    <li class="sep"></li>';
            $dropdown .= '    <li><a href="#" class="archive-dropdown-close">Lukk</a></li>';
            $dropdown .= '</ul>';
            
            // Build the markup
            $ret .= '<li' . $overlay . '>';
            $ret .= '    <div class="archive-item-dropdown">';
            $ret .= '        <div class="archive-item-dropdown-arrow">';
            $ret .= '            <i class="fa fa-caret-down"></i>';
            $ret .= '       </div>';
            $ret .= '       <div class="archive-dropdown-content"><p>Valg</p>' . $dropdown . '</div>';
            $ret .= '    </div>';
            $ret .= '    <a title="' . $title . '"' . $url_target . ' href="' . $url . '">';
            $ret .= '        <div class="archive-item" ' . implode(' ', $data) . '>';
            $ret .= '            ' . $div_flag;
            $ret .= '            <div class="archive-badge archive-badge-right hidden"><i class="fa fa-comments-o"></i></div>';
            $ret .= '            <div class="archive-item-icon" style="background-image: url(\'assets/images/icons/' . $image . '\');"></div>';
            $ret .= '            <div class="archive-item-label"><h4>' . $child->getName() . '</h4></div>';
            $ret .= '        </div>';
            $ret .= '    </a>';
            $ret .= '</li>';
        }

        // Return the content here
        return $ret;
    }
}