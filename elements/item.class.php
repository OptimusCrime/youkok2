<?php
/*
 * File: item.php
 * Holds: Class for either a directory or file in the system
 * Created: 09.04.14
 * Last updated: 14.04.14
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
    
    const DOWNLOADS_WEEK = 0;
    const DOWNLOADS_MONTH = 1;
    const DOWNLOADS_YEAR = 2;
    const DOWNLOADS_ALL = 3;
    
    private $collection;
    private $db;

    private $id;
    private $url;
    private $name;
    private $parent;
    private $location;
    private $urlFriendly;
    private $downloadCount;
    private $isDirectory;
    private $accepted;
    private $visible;
    private $mimeType;
    private $shouldLoadPhysicalLocation;
    private $fullLocation;
    private $loadedFlags;
    private $flags;

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
        $this->loadedFlags = false;
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
            $get_item_info = "SELECT name, parent, is_directory, url_friendly, mime_type, is_accepted, is_visible
            FROM archive 
            WHERE id = :id";
            
            $get_item_info_query = $this->db->prepare($get_item_info);
            $get_item_info_query->execute(array(':id' => $this->id));
            $row = $get_item_info_query->fetch(PDO::FETCH_ASSOC);
            
            // Set results
            $this->name = $row['name'];
            $this->isDirectory = $row['is_directory'];
            $this->urlFriendly = $row['url_friendly'];
            $this->parent = $row['parent'];
            $this->mimeType = $row['mime_type'];
            $this->accepted = $row['is_accepted'];
            $this->visible = $row['is_visible'];
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

    public function isDirectory() {
        return $this->isDirectory;
    }

    public function getMimeType() {
        return $this->mimeType;
    }

    public function isVisible() {
        return $this->visible;
    }

    public function isAccepted() {
        return $this->accepted;
    }

    public function getFullLocation() {
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
            }
            
            // Reverse array
            $temp_url = array_reverse($temp_url);
            
            // Store in real variable
            $this->url = $temp_url;
        }

        // Return goes here!
        return substr($path, 1) . implode('/', $this->url) . ($this->isDirectory ? '/' : '');
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
            $insert_user_download = "INSERT INTO download (file, ip, user)
            VALUES (:file, :ip, :user)";
            
            $insert_user_download_query = $this->db->prepare($insert_user_download);
            $insert_user_download_query->execute(array(':file' => $this->id, ':ip' => $_SERVER['REMOTE_ADDR'], ':user' => $user->getId()));
        }
        else {
            // Is not logged in
            $insert_anon_download = "INSERT INTO download (file, ip)
            VALUES (:file, :ip)";
            
            $insert_anon_download_query = $this->db->prepare($insert_anon_download);
            $insert_anon_download_query->execute(array(':file' => $this->id, ':ip' => $_SERVER['REMOTE_ADDR']));
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
}
?>