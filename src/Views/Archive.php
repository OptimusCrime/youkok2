<?php
/*
 * File: archive.controller.php
 * Holds: The ArchiveController-class
 * Created: 02.10.13
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

class Archive extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($routes) {
        // Calling Base' constructor
        parent::__construct($routes);
        
        // Displaying 404 or not
        $should_display_404 = false;
    
        // Check if base
        if ($this->queryGetClean('/') == $this->routes['archive'][0]) {
            // Turn on caching
            $this->template->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
        
            // Get title
            $this->template->assign('ARCHIVE_TITLE', '<h1>Kokeboka</h1>');
            $this->template->assign('ARCHIVE_MODE', 'list');
            
            // Check if cached
            if (!$this->template->isCached('archive.tpl', $this->queryGetClean())) {
                // Not cached, load courses
                $this->template->assign('ARCHIVE_DISPLAY', $this->loadCourses());

                // Get breadcrumbs
                $this->template->assign('ARCHIVE_BREADCRUMBS', '<li class="active">Kokeboka</li>');
            }
        }
        else {
            // Create new object
            $item = new Item($this);
            
            // Decide if we should load favorites
            if ($this->user->isLoggedIn()) {
                $item->setLoadFavorite(true);
            }
            
            // Create the object
            $item->createByUrl($this->queryGetClean());
            
            // Check if was found or invalid url
            if ($item->wasFound()) {
                // Add to collection if new
                $this->collection->add($item);
                
                // Element was found, double check that this is a directory
                if ($item->isDirectory()) {
                    // Assign variables we need no matter if the page is cached or not
                    $this->template->assign('ARCHIVE_ID', $item->getId());
                    
                    // User status
                    $this->template->assign('ARCHIVE_USER_BANNED', $this->user->isBanned());
                    $this->template->assign('ARCHIVE_USER_HAS_KARMA', $this->user->hasKarma());
                    $this->template->assign('ARCHIVE_USER_CAN_CONTRIBUTE', $this->user->canContribute());
                    $this->template->assign('ARCHIVE_USER_ONLINE', $this->user->isLoggedIn());
                    
                    // File types
                    $accepted_filetypes = explode(',', SITE_ACCEPTED_FILETYPES);
                    $this->template->assign('ARCHIVE_ACCEPTED_FILETYPES', json_encode($accepted_filetypes));
                    $accepted_fileending = explode(',', SITE_ACCEPTED_FILEENDINGS);
                    $this->template->assign('ARCHIVE_ACCEPTED_FILEENDINGS', json_encode($accepted_fileending));
                    
                    // Get title
                    $archive_title = '<h1>' . $item->getName() . '</h1>';
                    if ($item->hasCourse()) {
                        $archive_title .= '<span> - </span><h2>' . $item->getCourse()->getName() . '</h2>';
                    }
                    if ($this->user->isLoggedIn()) {
                        $archive_title .= ' <i class="fa fa-star archive-heading-star-' . $item->isFavorite() . '" data-archive-id="' . $item->getId() . '" id="archive-heading-star"></i>';
                    }
                    
                    // Description
                    $item_root = $item->getRootParent();
                    $site_description = $item_root->getName() . ' - ' . $item_root->getCourse()->getName() . ': Øvinger, løsningsforslag, gamle eksamensoppgaver og andre ressurser på Youkok2.com, den beste kokeboka på nettet.';
                    
                    // Assign to Smarty
                    $this->template->assign('ARCHIVE_TITLE', $archive_title);
                    $this->template->assign('ARCHIVE_ZIP_DOWNLOAD', $item->generateUrl($this->routes['download'][0]));
                    $this->template->assign('ARCHIVE_ZIP_DOWNLOAD_NUM', $item->getChildrenCount(Item::$file));
                    
                    // List every single element that has this element as a parent
                    $this->template->assign('ARCHIVE_DISPLAY', $this->loadArchive($item->getId()));
                    $this->template->assign('ARCHIVE_MODE', 'browse');
                    
                    // Get breadcrumbs
                    $this->template->assign('ARCHIVE_BREADCRUMBS', $this->loadBreadcrumbs($item));
                    
                    // Add title
                    $this->template->assign('SITE_TITLE', 'Kokeboka :: ' . $this->loadBredcrumbsTitle($item));
                    $this->template->assign('SITE_DESCRPTION', $site_description);
                }
                else {
                    $should_display_404 = true;
               }
            }
            else {
                $should_display_404 = true;
            }
        }
        
        // Check if return 404 or not
        if ($should_display_404) {
            $this->display404();
        }
        else {
            // Set menu
            $this->template->assign('HEADER_MENU', 'ARCHIVE');

            // Found (yay), display archive tpl
            $this->displayAndCleanup('archive.tpl', $this->queryGetClean());
        }
    }

    //
    // Method for loading breadcrumbs
    //

    private function loadBreadcrumbs($element) {
        // Some variables
        $ret = '';
        $collection = $element->getBreadcrumbs();
        $collection_size = count($collection);
        
        // Build return string
        $ret .= '<li><a href="' . substr($this->routes['archive'][0], 1) . '/">Arkiv</a></li>';

        // Loop and build list
        for ($i = 1; $i < $collection_size; $i++) {
            // Check what element
            if (($i + 1) == $collection_size) {
                // Is final element
                $ret .= '<li class="active">' . $collection[$i]->getName() . '</li>';
            }
            else {
                // Is not final element
                $ret .= '<li><a href="' . $collection[$i]->generateUrl($this->routes['archive'][0]) . '">' . $collection[$i]->getName() . '</a></li>';
            }
            
        }

        // Return breadcrumbs
        return $ret;
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

    private function loadArchive($id) {
        $ret = '';
        
        // Loading newest files from the system
        $get_current_archive = "SELECT id
        FROM archive
        WHERE parent = :parent
        AND is_visible = 1
        ORDER BY is_directory DESC,
        name ASC";
        
        $get_current_archive_query = $this->db->prepare($get_current_archive);
        $get_current_archive_query->execute(array(':parent' => $id));
        while ($row = $get_current_archive_query->fetch(PDO::FETCH_ASSOC)) {
            $item = new Item($this);
            
            // Decide if we should load favorites
            if ($this->user->isLoggedIn()) {
                $item->setLoadFavorite(true);
            }
            
            // Set load flags
            $item->setLoadFlagCount(true);
            
            // Create item
            $item->createById($row['id']);
            
            // Add to collection if new
            $this->collection->addIfDoesNotExist($item);

            // CHeck if element was loaded
            if ($item != null) {
                $flag_count = $item->getFlagCount();
                // Check if element is file or directory
                if ($item->isDirectory()) {
                    // This is a directory, link should go to archive
                    $ret .= '<li>
                                <a title="' . $item->getName() . '" href="' . $item->generateUrl($this->routes['archive'][0]) . '">
                                    <div class="archive-item' . ($item->isAccepted() ? '' : ' has-overlay' ) . '" data-favorite="' . $item->isFavorite($this->user) . '" data-id="' . $item->getId() . '" data-type="dir" data-name="' . $item->getName() . '" data-flags="' . $flag_count . '">
                                        ' . ($flag_count > 0 ? '<div class="archive-badge">' . $flag_count . '</div>' : '') . '
                                        <div class="archive-badge archive-badge-right hidden"><i class="fa fa-comments-o"></i></div>
                                        ' . ($item->isAccepted() ? '' : '<div class="archive-overlay"></div>') . '
                                        <div class="archive-item-icon" style="background-image: url(\'assets/css/lib/images/mimetypes64/folder.png\');"></div>
                                        <div class="archive-item-label"><h4>' . $item->getName() . '</h4></div>
                                    </div>
                                </a>
                            </li>';
                }
                else if ($item->isLink()) {
                    // This is a link
                    $ret .= '<li>
                                <a target="_blank" title="Link til: ' . $item->getUrl() . '" href="' . $item->generateUrl($this->routes['redirect'][0]) . '">
                                    <div class="archive-item' . ($item->isAccepted() ? '' : ' has-overlay' ) . '" data-favorite="' . $item->isFavorite($this->user) . '" data-id="' . $item->getId() . '" data-type="dir" data-name="' . $item->getName() . '" data-flags="' . $flag_count . '">
                                        ' . ($flag_count > 0 ? '<div class="archive-badge">' . $flag_count . '</div>' : '') . '
                                        <div class="archive-badge archive-badge-right hidden"><i class="fa fa-comments-o"></i></div>
                                        ' . ($item->isAccepted() ? '' : '<div class="archive-overlay"></div>') . '
                                        <div class="archive-item-icon" style="background-image: url(\'assets/css/lib/images/mimetypes64/link.png\');"></div>
                                        <div class="archive-item-label"><h4>' . $item->getName() . '</h4></div>
                                    </div>
                                </a>
                            </li>';
                }
                else {
                    // This is a file, link should go to download
                    $ret .= '<li>
                                <a title="' . $item->getName() . '" rel="nofollow" href="' . $item->generateUrl($this->routes['download'][0]) . '">
                                    <div class="archive-item' . ($item->isAccepted() ? '' : ' has-overlay' ) . '" data-favorite="' . $item->isFavorite($this->user) . '" data-id="' . $item->getId() . '" data-type="file" data-name="' . $item->getName() . '" data-flags="' . $flag_count . '" data-size="' . $item->getSize() . '">
                                        ' . ($flag_count > 0 ? '<div class="archive-badge">' . $flag_count . '</div>' : '') . '
                                        <div class="archive-badge archive-badge-right hidden"><i class="fa fa-comment"></i></div>
                                        ' . ($item->isAccepted() ? '' : '<div class="archive-overlay"></div>') . '
                                        <div class="archive-item-icon" style="background-image: url(\'assets/css/lib/images/mimetypes64/' . ($item->getMissingImage() ? 'unknown' : $item->getMimeType()) . '.png\');"></div>
                                        <div class="archive-item-label"><h4>' . $item->getName() . '</h4></div>
                                    </div>
                                </a>
                            </li>';
                }
            }
        }

        // Return the content here
        return $ret;
    }

    //
    // Load all the course
    //

    private function loadCourses() {
        // Variables are nice
        $ret = '';
        $letter = null;
        $container_is_null = true;
        $archive_url = substr($this->routes['archive'][0], 1);
        
        // Load all the courses
        $get_all_courses = "SELECT c.code, c.name, a.url_friendly
        FROM course c
        LEFT JOIN archive AS a ON c.id = a.course
        WHERE a.is_visible = 1
        ORDER BY c.code ASC";
        
        $get_all_courses_query = $this->db->prepare($get_all_courses);
        $get_all_courses_query->execute();
        while ($row = $get_all_courses_query->fetch(PDO::FETCH_ASSOC)) {
            // Store the current letter
            $current_letter = substr($row['code'], 0, 1);

            // Check how we should parse the course
            if ($container_is_null) {
                $ret .= '<div class="col-md-6 archive-course">
                    <h3>' . $current_letter . '</h3>
                    <ul>
                        <li>
                            <a href="' . $archive_url . '/' . $row['url_friendly'] . '">' . $row['code'] . ' - ' . $row['name'] . '</a>
                        </li>';

                $container_is_null = false;
            }
            else {
                if ($letter != $current_letter) {
                    $ret .= '</ul></div><div class="col-md-6 archive-course">
                    <h3>' . $current_letter . '</h3>
                    <ul>
                        <li>
                            <a href="' . $archive_url . '/' . $row['url_friendly'] . '">' . $row['code'] . ' - ' . $row['name'] . '</a>
                        </li>';
                }
                else {
                    $ret .= '<li>
                                <a href="' . $archive_url . '/' . $row['url_friendly'] . '">' . $row['code'] . ' - ' . $row['name'] . '</a>
                            </li>';
                }
            }
            
            // Assign new letter
            $letter = $current_letter;
        }

        // End container
        $ret .= '</ul></div>';

        // Return content
        return $ret;
    }
}

//
// Return the class name
//

return 'ArchiveController';