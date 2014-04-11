<?php
/*
 * File: item.php
 * Holds: Class for either a directory or file in the system
 * Created: 09.04.14
 * Last updated: 12.04.14
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
    private $urlFriendly;
    private $downloadCount;
    private $isDirectory;
    private $mimeType;

    //
    // Constructor
    //
    
    public function __construct($collection, $db) {
        // Set pointer to collection and db
        $this->collection = $collection;
        $this->db = $db;

        // Create array for url
        $this->url = array();

        // Create array for download numbers
        $this->downloadCount = array(0 => null, 1 => null, 2 => null, 3 => null);
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
                $get_revese_url = "SELECT id
                FROM archive 
                WHERE parent = :parent
                AND url_friendly = :url_friendly";
                
                $get_revese_url_query = $this->db->prepare($get_revese_url);
                $get_revese_url_query->execute(array(':parent' => $current_id, ':url_friendly' => $url_piece_single));
                $row = $get_revese_url_query->fetch(PDO::FETCH_ASSOC);
                
                // Check if anything was returned
                if (isset($row['id'])) {
                    // Was found, update the current id
                    $current_id = $row['id'];

                    // Add url piece
                    $this->url[] = $url_piece_single;
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
            $get_item_info = "SELECT name, parent, is_directory, url_friendly, mime_type
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
        while ($temp_id != 0) {
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
}
?>