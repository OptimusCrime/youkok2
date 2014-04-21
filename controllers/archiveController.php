<?php
/*
 * File: archiveController.php
 * Holds: The ArchiveController-class
 * Created: 02.10.13
 * Last updated: 18.04.14
 * Project: Youkok2
 * 
*/

//
// The ArchiceController
//

class ArchiveController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($paths, $base) {
        // Calling Base' constructor
        parent::__construct($paths, $base);
        
        // Displaying 404 or not
        $should_display_404 = false;

        // Check if base
        if (str_replace('/', '', $_GET['q']) == str_replace('/', '', $this->paths['archive'][0])) {
            // Currently displaying the base

            $this->template->assign('ARCHIVE_MODE', 'list');
            $this->template->assign('ARCHIVE_DISPLAY', $this->loadCourses());

            // Get title
            $this->template->assign('ARCHIVE_TITLE', 'Arkiv');

            // Get breadcrumbs
            $this->template->assign('ARCHIVE_BREADCRUMBS', '<li class="active">Arkiv</li>');
        }
        else {
            // Create new object
            $item = new Item($this->collection, $this->db);
            $item->createByUrl($_GET['q']);

            // Check if was found or invalid url
            if ($item->wasFound()) {
                // Item was found, store id
                $element_id = $item->getId();

                // Add to collection if new
                $this->collection->addIfDoesNotExist($item);

                // Load item from collection
                $element = $this->collection->get($element_id);
                
                // Check if element is null (this should not be possible, but just in case...)
                if ($element == null) {
                    // 404
                    $should_display_404 = true;
                }
                else {
                    // Element was found, double check that this is a directory
                    if ($element->isDirectory()) {
                        // Element was directory, now list every single element that has this element as a parent
                        $this->template->assign('ARCHIVE_DISPLAY', $this->loadArchive($element->getId()));
                        $this->template->assign('ARCHIVE_MODE', 'browse');

                        // Get title
                        $archive_title = $element->getName();
                        if ($this->user->isLoggedIn() and ($element->isFavorite($this->user) == 1 or $element->isFavorite($this->user) == 0)) {
                            $archive_title .= ' <small><i class="fa fa-star archive-heading-star-' . $element->isFavorite($this->user) . '" data-archive-id="' . $element->getId() . '" id="archive-heading-star"></i></small>';
                        }

                        // Assign to Smarty
                        $this->template->assign('ARCHIVE_TITLE', $archive_title);
                        $this->template->assign('ARCHIVE_ID', $element->getId());
                        $this->template->assign('ARCHIVE_USER_ONLINE', ($this->user->isLoggedIn() ? 'pizza' : 'nope'));

                        // Check if user is verified
                        $this->template->assign('ARCHIVE_USER_VERIFIED', $this->user->isVerified());

                        // Get breadcrumbs
                        $this->template->assign('ARCHIVE_BREADCRUMBS', $this->loadBreadcrumbs($element));

                        // Find accepted filetypes
                        $accepted_files = explode(',', SITE_ACCEPTED_FILETYPES);
                        $this->template->assign('ARCHIVE_ACCEPTED_FILETYPES', json_encode($accepted_files));
                    }
                    else {
                        $should_display_404 = true;
                    }
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
            $this->displayAndCleanup('archive.tpl');
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
        $ret .= '<li><a href="' . substr($this->paths['archive'][0], 1) . '/">Arkiv</a></li>';

        // Loop and build list
        for ($i = 1; $i < $collection_size; $i++) {
            // Check what element
            if (($i + 1) == $collection_size) {
                // Is final element
                $ret .= '<li class="active">' . $collection[$i]->getName() . '</li>';
            }
            else {
                // Is not final element
                $ret .= '<li><a href="' . $collection[$i]->generateUrl($this->paths['archive'][0]) . '">' . $collection[$i]->getName() . '</a></li>';
            }
            
        }

        // Return breadcrumbs
        return $ret;
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
            $item = new Item($this->collection, $this->db);
            $item->createById($row['id']);

            // Add to collection if new
            $this->collection->addIfDoesNotExist($item);

            // Load item from collection
            $element = $this->collection->get($row['id']);

            // CHeck if element was loaded
            if ($element != null) {
                $flag_count = $element->getFlagCount();
                // Check if element is file or directory
                if ($element->isDirectory()) {
                    // This is a directory, link should go to archive
                    $ret .= '<li>
                                <a title="' . $element->getName() . '" href="' . $element->generateUrl($this->paths['archive'][0]) . '">
                                    <div class="archive-item' . ($element->isAccepted() ? '' : ' has-overlay' ) . '" data-favorite="' . $element->isFavorite($this->user) . '" data-id="' . $element->getId() . '" data-type="dir" data-name="' . $element->getName() . '" data-flags="' . $flag_count . '">
                                        ' . ($flag_count > 0 ? '<div class="archive-badge">' . $flag_count . '</div>' : '') . '
                                        ' . ($element->isAccepted() ? '' : '<div class="archive-overlay"></div>') . '
                                        <div class="archive-item-icon" style="background-image: url(\'assets/css/lib/images/mimetypes64/folder.png\');"></div>
                                        <div class="archive-item-label"><p>' . $element->getName() . '</p></div>
                                    </div>
                                </a>
                            </li>';
                }
                else {
                    // This is a file, link should go to download
                    $ret .= '<li>
                                <a title="' . $element->getName() . '" href="' . $element->generateUrl($this->paths['download'][0]) . '">
                                    <div class="archive-item' . ($element->isAccepted() ? '' : ' has-overlay' ) . '" data-favorite="' . $element->isFavorite($this->user) . '" data-id="' . $element->getId() . '" data-type="file" data-name="' . $element->getName() . '" data-flags="' . $flag_count . '" data-size="' . $element->getSize() . '">
                                        ' . ($flag_count > 0 ? '<div class="archive-badge">' . $flag_count . '</div>' : '') . '
                                        ' . ($element->isAccepted() ? '' : '<div class="archive-overlay"></div>') . '
                                        <div class="archive-item-icon" style="background-image: url(\'assets/css/lib/images/mimetypes64/' . $item->getMimeType() . '.png\');"></div>
                                        <div class="archive-item-label"><p>' . $element->getName() . '</p></div>
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
        $archive_url = substr($this->paths['archive'][0], 1);
        
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
// Loading the class-name dynamically and creating an instance doing our magic
//

// Getting the current file-path
$path = explode('/', __FILE__);

// Including the run-script to execute it all
include_once 'run.php';
?>