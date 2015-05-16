<?php
/*
 * File: Archive.php
 * Holds: Archive view
 * Created: 02.10.13
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
 * The Home class, extending Base class
 */

class Archive extends Base {

    /*
     * Constructor
     */

    public function __construct() {
        parent::__construct();
        
        // Set information to site data
        $this->addSiteData('user_online', Me::isLoggedIn());
        $this->addSiteData('user_can_contribute', Me::canContribute());
        
        // Set information directly to Smarty
        $this->template->assign('ARCHIVE_USER_CAN_CONTRIBUTE', 'ARCHIVE');
        $this->template->assign('ARCHIVE_USER_BANNED', 'ARCHIVE');
        $this->template->assign('ARCHIVE_USER_HAS_KARMA', 'ARCHIVE');
        
        // Load the archive
        $this->loadArchive();

        // Set stuff
        $this->template->assign('HEADER_MENU', 'ARCHIVE');
        $this->template->assign('ARCHIVE_PATH', Routes::ARCHIVE);

        // Display
        $this->displayAndCleanup('archive.tpl', $this->queryGetClean());
    }
    
    /*
     * Load archive for a directory
     */
    
    private function loadArchive() {
        // Try to create new element
        $element = new Element();
        $element->createByUrl($this->queryGetClean());

        // Check if element was found and is directory
        if ($element->controller->wasFound() and $element->isDirectory()) {
            // Set archive information to Smarty
            $this->setArchiveInformation();
            
            // Set information related to the Element
            $this->setElementInformation($element);
        }
        else {
            $this->display404();
        }
    }
    
    /*
     * Set information needed in the archive view
     */
    
    private function setArchiveInformation() {
        // Set state
        $this->template->assign('HEADER_MENU', 'ARCHIVE');

        // Set user status
        Me::setUserStatus($this, 'ARCHIVE');

        // File types
        $accepted_fileending = explode(',', ACCEPTED_FILEENDINGS);
        $this->addSiteData('file_types', $accepted_fileending);
    }
    
    /*
     * Set information related to the Element we are browsing
     */
    
    private function setElementInformation($element) {
        // Set id
        $this->addSiteData('archive_id', $element->getId());
        
        // Set title
        if ($element->controller->isCourse()) {
            $course = $element->controller->getCourse();
            $archive_title = '<h1>' . $course['code'] . '</h1>';
            $archive_title .= '<span> &mdash; </span><h2>' . $course['name'] . '</h2>';
            
            // Check if the course has an exam date
            if ($element->getExam() !== null and strlen($element->getExam()) > 0 and strtotime($element->getExam()) > time()) {
                $this->template->assign('ARCHIVE_EXAM', $element->getExam());
                $this->template->assign('ARCHIVE_EXAM_PRETTY', Utilities::prettifySQLDate($element->getExam()));
            }
        }
        else {
            $archive_title = '<h1>' . $element->getName() . '</h1>';
        }
        
        // Check if we should add the star for adding / removing favorites too
        if (Me::isLoggedIn()) {
            $archive_title .= ' <i class="fa fa-star archive-heading-star-' . Me::isFavorite($element->getId()) . '" data-archive-id="' . $element->getId() . '" id="archive-heading-star"></i>';
        }
        $this->template->assign('ARCHIVE_TITLE', $archive_title);
        
        // Metadata
        if (!$element->controller->isCourse()) {
            $element_root = $element->controller->getRootParent();
            $course = $element_root->controller->getCourse();
        }
        
        $site_description = $course['code'] . ' &mdash; ' . $course['name'] . ': Øvinger, løsningsforslag, gamle eksamensoppgaver og andre ressurser på Youkok2.com, den beste kokeboka på nettet.';
        $this->template->assign('SITE_DESCRPTION', $site_description);
        
        // Get breadcrumbs
        $this->template->assign('ARCHIVE_BREADCRUMBS', $this->loadBreadcrumbs($element));
        
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
        $collection = $element->controller->getParents();
        
        // Loop and build list
        foreach ($collection as $v) {
            // Check if si course
            if ($v->controller->isCourse()) {
                $course = $v->controller->getCourse();
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
                $ret[] = '<li><a href="' . $v->controller->generateUrl(Routes::ARCHIVE) . '">' . $name . '</a></li>';
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
        $children = $element->controller->getChildren();
        
        // Loop children
        foreach ($children as $child) {
            $data = array();
            $url_target = '';
            
            // Some stuff
            if ($child->isDirectory()) {
                // Directory
                $title = $child->getName();
                $url = $child->controller->generateUrl(Routes::ARCHIVE);
                $image = 'folder.png';
                $type = 'dir';
            }
            else if ($child->isLink()) {
                // Link
                $title = 'Link til: ' . $child->getUrl();
                $url = $child->controller->generateUrl(Routes::REDIRECT);
                $url_target = ' target="_blank"';
                $image = 'link.png';
                $type = 'link';
            }
            else {
                // Normal file
                $title = $child->getName();
                $url = $child->controller->generateUrl(Routes::DOWNLOAD);
                $url_target = ' target="_blank"';
                $image = (($child->getMissingImage() == 1) ? 'unknown' : $child->getMimeType()) . '.png';
                $type = 'file';
                $data[] = 'data-size="' . $child->getSize() . '"';
            }
            
            // Flag
            $div_flag = '';
            if ($child->controller->getFlagCount() > 0) {
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
            $data[] = 'data-flags="' . $child->controller->getFlagCount() . '"';
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