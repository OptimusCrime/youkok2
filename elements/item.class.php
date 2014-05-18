<?php
/*
 * File: item.php
 * Holds: Class for either a directory or file in the system
 * Created: 09.04.14
 * Last updated: 18.05.14
 * Project: Youkok2
 * 
*/

//
// Either directory or file in the system
//

class Item {
    
    //
    // Variables for the class
    //
    
    private $collection;
    private $db;

    private $id;
    private $url;
    private $name;
    private $parent;
    private $location;
    private $added;
    private $size;
    private $urlFriendly;
    private $downloadCount;
    private $directory;
    private $favorite;
    private $accepted;
    private $visible;
    private $mimeType;
    private $missingImage;
    private $shouldLoadPhysicalLocation;
    private $shouldLoadRoot;
    private $rootParent;
    private $fullLocation;
    private $loadedFlags;
    private $flags;
    private $course;
    private $courseName;

    //
    // Constructor
    //
    
    public function __construct($collection, $db) {
        // Set pointer to collection and db
        $this->collection = $collection;
        $this->db = $db;

        // Create arrays
        $this->url = array();
        $this->fullLocation = array();
        $this->flags = array();

        // Create array for download numbers
        $this->downloadCount = array(0 => null, 1 => null, 2 => null, 3 => null);

        // Set shouldLoadPhysicalLocation (lol) to false and other stuff
        $this->shouldLoadPhysicalLocation = false;
        $this->shouldLoadRoot = false;
        $this->loadedFlags = false;
        $this->favorite = 'null';
        $this->rootParent = null;
        $this->size = 0;
        $this->course = null;
        $this->courseName = '';
        $this->missingImage = false;
    }
    
    //
    // Create methods
    //
    
    public function createById($id) {
        $this->id = $id;
    }
    
    public function createByUrl($url) {
        // Explode the url
        $url_pieces_temp = explode('/', $url);

        // Clean the pieces
        $url_pieces = array();
        foreach ($url_pieces_temp as $k => $v) {
            if ($k > 0 and strlen($v) > 0) {
                $url_pieces[] = $v;
            }
        }
        
        // Only continue if we have more than one elements
        if (count($url_pieces) > 0) {
            // Set current id to root
            $current_id = 1;
            
            // Loop each fragment
            foreach ($url_pieces as $url_piece_single) {
                // Todo add caching here!
                $get_reverse_url = "SELECT id". ($this->shouldLoadPhysicalLocation ? ', location' : '') . "
                FROM archive 
                WHERE parent = :parent
                AND url_friendly = :url_friendly
                AND is_visible = 1";
                
                $get_reverse_url_query = $this->db->prepare($get_reverse_url);
                $get_reverse_url_query->execute(array(':parent' => $current_id, ':url_friendly' => $url_piece_single));
                $row = $get_reverse_url_query->fetch(PDO::FETCH_ASSOC);
                
                // Check if anything was returned
                if (isset($row['id'])) {
                    // Was found, update the current id
                    $current_id = $row['id'];

                    // Add url piece
                    $this->url[] = $url_piece_single;

                    // Should cache, just in case
                    $temp_item = new Item($this->collection, $this->db);
                    $temp_item->createById($current_id);
                    $temp_item->collection->addIfDoesNotExist($temp_item);

                    // Check if we should add to location array too
                    if ($this->shouldLoadPhysicalLocation) {
                        $this->fullLocation[] = $row['location'];
                    }
                }
            }

            // Check if number of fragments are equal
            if (count($url_pieces) == count($this->url)) {
                $this->id = $current_id;
            }
        }
    }

    //
    // Do the acutal creation here
    //

    public function create() {
        // Get all info about file
        if ($this->id != null) {
            // Id is set, run a simple query
            $get_item_info = "SELECT name, parent, is_directory, url_friendly, mime_type, missing_image, is_accepted, is_visible, added, location, size, course
            FROM archive 
            WHERE id = :id";
            
            $get_item_info_query = $this->db->prepare($get_item_info);
            $get_item_info_query->execute(array(':id' => $this->id));
            $row = $get_item_info_query->fetch(PDO::FETCH_ASSOC);
            
            // Set results
            $this->name = $row['name'];
            $this->directory = $row['is_directory'];
            $this->urlFriendly = $row['url_friendly'];
            $this->parent = $row['parent'];
            $this->mimeType = $row['mime_type'];
            $this->missingImage = $row['missing_image'];
            $this->accepted = $row['is_accepted'];
            $this->visible = $row['is_visible'];
            $this->location = $row['location'];
            $this->added = $row['added'];
            $this->size = $row['size'];
            $this->course = $row['course'];
        }
    }

    //
    // Check if real or invalid url
    //

    public function wasFound() {
        if ($this->id != null and is_numeric($this->id)) {
            return true;
        }
        else {
            return false;
        }
    }

    //
    // Getters
    //

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getUrlFriendly() {
        return $this->urlFriendly;
    }

    public function getParent() {
        return $this->parent;
    }

    public function getLocation() {
        return $this->location;
    }

    public function isDirectory() {
        return $this->directory;
    }

    public function getMimeType() {
        return $this->mimeType;
    }
    
    public function getMissingImage() {
        return $this->missingImage;
    }

    public function getAdded() {
        return $this->added;
    }

    public function getSize() {
        return $this->size;
    }

    public function isVisible() {
        return $this->visible;
    }

    public function isAccepted() {
        return $this->accepted;
    }

    //
    // Returning the full location for the current file
    //

    public function getFullLocation() {
        if (count($this->fullLocation) == 0) {
            $temp_location = array($this->location);
            $temp_id = $this->parent;

            // Loop untill we reach the root
            while ($temp_id != 0) {
                // Check if this object already exists
                $temp_item = $this->collection->get($temp_id);
                if ($temp_item == null) {
                    // Create new object
                    $temp_item = new Item($this->collection, $this->db);
                    $temp_item->createById($temp_id);
                    $temp_item->collection->add($temp_item);
                }

                // Get the url piece
                $temp_location[] = $temp_item->getLocation();

                // Update id
                $temp_id = $temp_item->getParent();
            }

            
            // Reverse array
            $temp_location = array_reverse($temp_location);

            // Assign
            $this->fullLocation = $temp_location;
        }

        return implode('/', $this->fullLocation);
    }

    //
    // This variable decides if the location is fetched too
    //

    public function setShouldLoadPhysicalLocation($b) {
        $this->shouldLoadPhysicalLocation = $b;
    }

    //
    // Generate url for this item
    //

    public function generateUrl($path) {
        // Check if the url is already cached!
        if (count($this->url) == 0) {
            // Store some variables for later
            $temp_url = array($this->urlFriendly);
            $temp_id = $this->parent;
            $temp_root_parent = $this;

            // Loop untill we reach the root
            while ($temp_id != 0) {
                // Check if this object already exists
                $temp_item = $this->collection->get($temp_id);
                if ($temp_item == null) {
                    // Create new object
                    $temp_item = new Item($this->collection, $this->db);
                    $temp_item->createById($temp_id);
                    $temp_item->collection->add($temp_item);
                }

                // Get the url piece
                $temp_url[] = $temp_item->getUrlFriendly();

                // Update id
                $temp_id = $temp_item->getParent();

                if ($temp_item->getId() != 1) {
                    $temp_root_parent = $temp_item;
                }
            }

            // Check if we should store the root
            if ($this->shouldLoadRoot) {
                $this->rootParent = $temp_root_parent;
            }
            
            // Reverse array
            $temp_url = array_reverse($temp_url);
            
            // Store in real variable
            $this->url = $temp_url;
        }

        // Return goes here!
        return substr($path, 1) . implode('/', $this->url) . ($this->directory ? '/' : '');
    }

    //
    // Get breadcrumbs for the current item
    //

    public function getBreadcrumbs() {
        // Store some variables for later
        $temp_collection = array($this);
        $temp_id = $this->parent;
        
        // Loop untill we reach the root
        while ($temp_id != null) {
            // Check if this object already exists
            $temp_item = $this->collection->get($temp_id);
            
            // Get the url piece
            $temp_collection[] = $temp_item;

            // Update id
            $temp_id = $temp_item->getParent(); 
        }

        // Return breadcrumbs in correct order here
        return array_reverse($temp_collection);
    }

    //
    // Download methods
    //
    
    public function getDownloadCount($delta) {
        // Check if null
        if ($this->downloadCount[$delta] == null) {
            // TODO, check what is fetched!

            // This count is not cached, run query to fetch the download number
            $get_download_count = "SELECT COUNT(d.id) as 'downloaded_times'
            FROM download d
            WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
            AND d.file = :file";
            
            $get_download_count_query = $this->db->prepare($get_download_count);
            $get_download_count_query->execute(array(':file' => $this->id));
            $row = $get_download_count_query->fetch(PDO::FETCH_ASSOC);

            // Set value
            $this->downloadCount[$delta] = $row['downloaded_times'];

            // Return value
            return $row['downloaded_times'];
        }
        else {
            // Cached result, just return
            return $this->downloadCount[$delta];
        }
    }
    
    public function setDownloadCount($delta, $value) {
        $this->downloadCount[$delta] = $value;
    }

    public function addDownload($user) {
        // Check if user is logged in
        if ($user != null and $user->isLoggedIn()) {
            // User is logged in
            $insert_user_download = "INSERT INTO download (file, ip, agent, user)
            VALUES (:file, :ip, :agent, :user)";
            
            $insert_user_download_query = $this->db->prepare($insert_user_download);
            $insert_user_download_query->execute(array(':file' => $this->id, ':ip' => $_SERVER['REMOTE_ADDR'], ':agent' => $_SERVER['HTTP_USER_AGENT'], ':user' => $user->getId()));
        }
        else {
            // Is not logged in
            $insert_anon_download = "INSERT INTO download (file, ip, agent)
            VALUES (:file, :ip, :agent)";
            
            $insert_anon_download_query = $this->db->prepare($insert_anon_download);
            $insert_anon_download_query->execute(array(':file' => $this->id, ':ip' => $_SERVER['REMOTE_ADDR'], ':agent' => $_SERVER['HTTP_USER_AGENT']));
        }
    }

    //
    // Flags
    //

    public function loadFlags() {
        $get_all_flags = "SELECT *
        FROM flag
        WHERE file = :file
        AND active = 1";
        
        $get_all_flags_query = $this->db->prepare($get_all_flags);
        $get_all_flags_query->execute(array(':file' => $this->id));
        while ($row = $get_all_flags_query->fetch(PDO::FETCH_ASSOC)) {
            $this->flags[] = $row;
        }
    }

    public function getFlagCount() {
        if ($this->loadedFlags == false) {
            // Flags are not loaded, load them first
            $this->loadFlags();
        }

        return count($this->flags);
    }

    public function getFlags() {
        if ($this->loadedFlags == false) {
            // Flags are not loaded, load them first
            $this->loadFlags();
        }
        
        return $this->flags;
    }

    //
    // Favorite
    //

    public function isFavorite($user) {
        // First, check if logged in
        if ($user->isLoggedIn()) {
            // Check if fetched
            if ($this->favorite === 'null') {
                // Not fetched
                $get_favorite_status = "SELECT id
                FROM favorite
                WHERE file = :file
                AND user = :user";
                
                $get_favorite_status_query = $this->db->prepare($get_favorite_status);
                $get_favorite_status_query->execute(array(':file' => $this->id, ':user' => $user->getId()));
                $row = $get_favorite_status_query->fetch(PDO::FETCH_ASSOC);
                
                // Cache for later
                if (isset($row['id'])) {
                    $this->favorite = 1;
                }
                else {
                    $this->favorite = 0;
                }

                // Return
                return $this->favorite;
            }
            else {
                // Fetched, just return
                return $this->favorite;
            }
        }
        else {
            return 'null';
        }
    }

    //
    // Root parent
    //

    public function setShouldLoadRoot($b) {
        $this->shouldLoadRoot = $b;
    }

    public function getRootParent() {
        return $this->rootParent;
    }

    //
    // Course
    //

    public function hasCourse() {
        return (($this->course == null) ? false : true);
    }

    public function getCouseName() {
        if ($this->courseName == '') {
            $get_course = "SELECT name
            FROM course
            WHERE id = :id";
            
            $get_course_query = $this->db->prepare($get_course);
            $get_course_query->execute(array(':id' => $this->course));
            $row = $get_course_query->fetch(PDO::FETCH_ASSOC);

            if (isset($row['name'])) {
                // Set name
                $this->courseName = $row['name'];
            }
        }

        // Finally return
        return $this->courseName;
    }
}
?>