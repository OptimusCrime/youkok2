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


/*
 * The Home class, extending Base class
 */

class Archive extends Base {

    /*
     * Constructor
     */

    public function __construct() {
        parent::__construct();
        
        // Check if base
        if ($this->queryGetClean('/') == Routes::ARCHIVE) {
            // Turn on caching
            $this->template->setCaching(\Smarty::CACHING_LIFETIME_CURRENT);

            // Set menu
            $this->template->assign('HEADER_MENU', 'ARCHIVE');

            // Check if cached
            if (!$this->template->isCached('courses.tpl', $this->queryGetClean())) {
                // Get title
                $this->template->assign('ARCHIVE_TITLE', '<h1>Emner</h1>');

                // Get breadcrumbs
                $this->template->assign('ARCHIVE_BREADCRUMBS', '<li class="active">Emner</li>');

                // Load content
                $this->loadCourses();
            }

            // Display
            $this->displayAndCleanup('courses.tpl', $this->queryGetClean());
        }
        else {
            // Load the archive
            $this->loadArchive();

            // Set stuff
            $this->template->assign('HEADER_MENU', 'ARCHIVE');
            $this->template->assign('ARCHIVE_PATH', Routes::ARCHIVE);

            // Display
            $this->displayAndCleanup('archive.tpl', $this->queryGetClean());
        }
    }


    /*
     * Load courses
     */

    private function loadCourses() {
        // Variables are nice
        $ret = '';
        $letter = null;
        $container_is_null = true;
        $new_row = false;

        // Load all the courses
        $get_all_courses  = "SELECT id, name, url_friendly, empty" . PHP_EOL;
        $get_all_courses .= "FROM archive" . PHP_EOL;
        $get_all_courses .= "WHERE parent IS NULL" . PHP_EOL;
        $get_all_courses .= "AND is_visible = 1" . PHP_EOL;
        $get_all_courses .= "ORDER BY name ASC";
        
        $get_all_courses_query = Database::$db->query($get_all_courses);
        while ($row = $get_all_courses_query->fetch(\PDO::FETCH_ASSOC)) {
            $element = new Element();
            $element->createById($row['id'], true);
            
            // Override attributes
            $element->setName($row['name']);
            $element->setEmpty($row['empty']);
            $element->setUrlFriendly($row['url_friendly']);
            
            // Check if element is course
            if ($element->controller->isCourse()) {
                // Get course
                $course = $element->controller->getCourse();
                // Store the current letter
                $current_letter = substr($course['code'], 0, 1);

                // Check how we should parse the course
                if ($container_is_null) {
                    $ret .= '<div class="row">' . PHP_EOL;
                    $ret .= '    <div class="col-xs-12 col-md-6 course-box">' . PHP_EOL;
                    $ret .= '        <h3>' . $current_letter . '</h3>' . PHP_EOL;
                    $ret .= '        <ul class="list-group">' . PHP_EOL;

                    $container_is_null = false;
                }
                else {
                    if ($letter != $current_letter) {
                        $ret .= '        </ul>' . PHP_EOL;
                        $ret .= '    </div>' . PHP_EOL;
                        
                        if ($new_row) {
                            $ret .= '</div>' . PHP_EOL;
                            $ret .= '<div class="row">' . PHP_EOL;
                        }
                        
                        $new_row = !$new_row;
                        
                        $ret .= '    <div class="col-xs-12 col-md-6 course-box">' . PHP_EOL;
                        $ret .= '        <h3>' . $current_letter . '</h3>' . PHP_EOL;
                        $ret .= '        <ul class="list-group">' . PHP_EOL;
                    }
                }

                $ret .= '            <li class="' . (($element->isEmpty()) ? 'course-empty ' : '') . 'list-group-item">' . PHP_EOL;
                $ret .= '                <a href="' . $element->controller->generateUrl(Routes::ARCHIVE) . '"><strong>' . $course['code'] . '</strong> &mdash; ' . $course['name'] . '</a>' . PHP_EOL;
                $ret .= '            </li>' . PHP_EOL;
                
                // Assign new letter
                $letter = $current_letter;
            }
        }

        // End container
        $ret .= '        </ul>' . PHP_EOL;
        $ret .= '    </div>' . PHP_EOL;
        $ret .= '</div>' . PHP_EOL;

        // Return content
        $this->template->assign('ARCHIVE_DISPLAY', $ret);
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
        $accepted_filetypes = explode(',', ACCEPTED_FILETYPES);
        $this->template->assign('ACCEPTED_FILETYPES', json_encode($accepted_filetypes));
        $accepted_fileending = explode(',', ACCEPTED_FILEENDINGS);
        $this->template->assign('ACCEPTED_FILEENDINGS', json_encode($accepted_fileending));
    }
    
    /*
     * Set information related to the Element we are browsing
     */
    
    private function setElementInformation($element) {
        // Set id
        $this->template->assign('ARCHIVE_ID', $element->getId());
        
        // Set title
        if ($element->controller->isCourse()) {
            $course = $element->controller->getCourse();
            $archive_title = '<h1>' . $course['code'] . '</h1>';
            $archive_title .= '<span> &mdash; </span><h2>' . $course['name'] . '</h2>';
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
            $div_overlay = '';
            if (!$child->isAccepted()) {
                $div_overlay = '<div class="archive-overlay"></div>';
            }
            
            // Collect data
            $data[] = 'data-id="' . $child->getId() . '"';
            $data[] = 'data-type="' . $type . '"';
            $data[] = 'data-name="' . $child->getName() . '"';
            $data[] = 'data-flags="' . $child->controller->getFlagCount() . '"';
            $data[] = 'data-favorite="' . (int) Me::isFavorite($child->getId()) . '"';
            
            // Build the markup
            $ret .= '<li>';
            $ret .= '    <a title="' . $title . '"' . $url_target . ' href="' . $url . '">';
            $ret .= '        <div class="archive-item" ' . implode(' ', $data) . '>';
            $ret .= '            ' . $div_flag;
            $ret .= '            ' . $div_overlay;
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