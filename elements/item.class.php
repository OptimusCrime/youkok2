<?php
/*
 * File: item.php
 * Holds: Class for either a directory or file in the system
 * Created: 09.04.14
 * Last updated: 11.04.14
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
        $this->url = $url;
    }

    public function create() {
        // Get all info about file
        if ($this->id != null) {
            // Id is set, run a simple query
            $get_item_info = "SELECT name, parent, is_directory, url_friendly
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

    //
    // Setters
    //

    public function setName($name) {
        $this->name = $name;
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

        // Return goes here! (TODO check if endfix with / if directory!)
        return $path . implode('/', $this->url);
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