
<?php
/*
 * File: archiveController.php
 * Holds: The ArchiveController-class
 * Created: 02.10.13
 * Last updated: 12.04.14
 * Project: Youkok2
 * 
*/

//
// The REST-class doing most of the magic
//

class ArchiveController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($paths, $base) {
        // Calling Base' constructor
        parent::__construct($paths, $base);
        // Include item class
        require_once $this->basePath . '/elements/item.class.php';

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
                echo '404....';
            }
            else {
                // Element was found, double check that this is a directory
                if ($element->isDirectory()) {
                    // Element was directory, now list every single element that has this element as a parent
                    $this->template->assign('ARCHIVE_DISPLAY', $this->loadArchive($element->getId()));

                    // Get title
                    $this->template->assign('ARCHIVE_TITLE', $element->getName());

                    // Get breadcrumbs
                    $this->template->assign('ARCHIVE_BREADCRUMBS', $this->loadBreadcrumbs($element));
                }
                else {
                    echo 'wtf...';
                }
            }
        }
        else {
            echo '404....';
        }

        // Kill database-connection and cleanup before displaying
        $this->close();
        
        // Display the template
        $this->template->display('archive.tpl');
    }

    //
    // Method for loading breadcrumbs
    //

    private function loadBreadcrumbs($element) {
        // Some variables
        $ret = '';
        $collection = $element->getBreadcrumbs();
        $collection_size = count($collection);
        
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
                // Check if element is file or directory
                if ($element->isDirectory()) {
                    // This is a directory, link should go to archive
                    $ret .= '<li>
                                <a href="' . $element->generateUrl($this->paths['archive'][0]) . '">
                                    <div class="archive-item">
                                        <div class="archive-item-icon" style="background-image: url(\'assets/css/lib/images/mimetypes64/folder.png\');"></div>
                                        <div class="archive-item-label"><p>' . $element->getName() . '</p></div>
                                    </div>
                                </a>
                            </li>';
                }
                else {
                    // This is a file, link should go to download
                    $ret .= '<li>
                                <a href="' . $element->generateUrl($this->paths['download'][0]) . '">
                                    <div class="archive-item">
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
}

//
// Loading the class-name dynamically and creating an instance doing our magic
//

// Getting the current file-path
$path = explode('/', __FILE__);

// Including the run-script to execute it all
include_once 'run.php';
?>