<?php
/*
 * File: item.php
 * Holds: Class for either a directory or file in the system
 * Created: 09.04.14
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
    
    // Pointer to the controller
    private $controller;

    // The query
    private $query;
    
    // Fields in the table
    private $id;
    private $name;
    private $urlFriendly;
    private $parent;
    private $course;
    private $location;
    private $mimeType;
    private $missingImage;
    private $size;
    private $directory;
    private $accepted;
    private $visible;
    private $added;
    
    // Full paths
    private $fullUrl;
    private $fullLocation;
    
    // Load additional information
    private $loadFullLocation;
    private $loadFavorite;
    private $loadFlagCount;
    
    // Other stuff
    private $favorite;
    private $rootParent;
    private $flagCount;
    private $downloadCount;
    
    // Pointers to other objects related to this item
    private $courseObj;
    private $flags;
    
    // Cache
    private $cache;

    // TODO
    private $shouldLoadRoot;
    
    //
    // Constructor
    //
    
    public function __construct($controller) {
        // Set pointer to the controller
        $this->controller = &$controller;
        
        // Init array for the query
        $this->query = array('select' => array('a.name', 'a.parent', 'a.is_directory', 'a.url_friendly', 
                                               'a.mime_type', 'a.missing_image', 'a.is_accepted', 'a.is_visible', 
                                               'a.added', 'a.location', 'a.size', 'a.course'), 
                             'join' => array(), 
                             'where' => array('WHERE a.id = :id'),
                             'groupby' => array(),
                             'execute' => array());

        // Variables to keep track of what should be loaded at creation
        $this->loadFullLocation = false;
        $this->loadFavorite = false;
        $this->loadFlagCount = false;
        $this->shouldLoadRoot = false; // TODO

        // Create arrays for full locations
        $this->fullUrl = array();
        $this->fullLocation = array();
        
        // Other stuff
        $this->favorite = null;
        $this->rootParent = null;
        $this->flagCount = null;
        $this->downloadCount = array(0 => null, 
                                     1 => null, 
                                     2 => null, 
                                     3 => null);
        
        // Set pointers to other objects
        $this->courseObj = null;
        $this->flags = array();

        // Set caching to true as default
        $this->cache = true;
    }
    
    //
    // Create methods
    //
    
    public function createById($id) {
        $this->id = $id;

        // Check if we should check the cache and if it is cached
        if ($this->cache and $this->controller->cacheManager->isCached($id)) {
            // This Item is cached, go ahead and fetch data
            $temp_cache_data = $this->controller->cacheManager->getCache($id);
            $fields = array('name', 'directory', 'urlFriendly', 'parent', 'mimeType', 'missingImage', 
                            'accepted', 'visible', 'location', 'added', 'size', 'course');
            
            // Loop all the fields and apply data
            foreach ($temp_cache_data as $k => $v) {
                $this->$k = $v;
            }

            // If we are fetching the full location, this should be the last fragment
            if ($this->loadFullLocation) {
                $this->fullLocation[] = $temp_cache_data['location'];
            }
        }
        else {
            // Add id to dynamic query
            $this->query['execute'][':id'] = $this->id;
            
            // Create dynamic query
            if ($this->loadFavorite) {
                $this->query['select'][] = "f.id AS 'favorite'";
                $this->query['join'][] = PHP_EOL . 'LEFT JOIN favorite AS f ON f.file = a.id AND f.user = :user';
                
                if (!isset($this->query['execute'][':user'])) {
                    $this->query['execute'][':user'] = $this->controller->user->getId();
                }
            }
            if ($this->loadFlagCount) {
                $this->query['select'][] = "count(fl.id) as 'flags'";
                $this->query['join'][] = PHP_EOL . 'LEFT JOIN flag as fl on a.id = fl.file';
                $this->query['groupby'][] = 'a.id';
            }
            
            // Create the actual query
            $get_item_info = 'SELECT ' . implode(', ', $this->query['select']) . PHP_EOL . 'FROM archive AS a ';
            
            // Add joins (if there are any)
            if (count($this->query['join']) > 0) {
                $get_item_info .= implode(' ', $this->query['join']);
            }
            
            // Add where
            $get_item_info .= PHP_EOL . 'WHERE a.id = :id ';
            
            // Add group by (again, if there are any)
            if (count($this->query['groupby']) > 0) {
                $get_item_info .= PHP_EOL . 'GROUP BY ' . implode(', ', $this->query['groupby']);
            }
            
            // Run the actual query
            $get_item_info_query = $this->controller->db->prepare($get_item_info);
            $get_item_info_query->execute($this->query['execute']);
            $row = $get_item_info_query->fetch(PDO::FETCH_ASSOC);
            
            // Set special fields
            if ($this->loadFavorite) {
                $this->favorite = (!isset($row['favorite']) or $row['favorite'] == null) ? false : true;
            }
            if (isset($row['flags'])) {
                $this->flagCount = $row['flags'];
            }
            
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
            
            // If we are fetching the full location, this should be the last fragment
            if ($this->loadFullLocation) {
                $this->fullLocation[] = $row['location'];
            }

            // Check if we should cache this Item
            if ($this->cache) {
                $this->controller->cacheManager->setCache($id, $this->cacheFormat());
            }
        }
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
        
        // Count number of fragments
        $num_pieces = count($url_pieces);
        
        // Only continue if we have more than one elements
        if ($num_pieces > 0) {
            // Set current id to root
            $temp_id = 1;
            
            // Loop each fragment
            foreach ($url_pieces as $k => $url_piece_single) {
                // Run query for this fragment
                $get_reverse_url = "SELECT id
                FROM archive 
                WHERE parent = :parent
                AND url_friendly = :url_friendly
                AND is_visible = 1";
                
                $get_reverse_url_query = $this->controller->db->prepare($get_reverse_url);
                $get_reverse_url_query->execute(array(':parent' => $temp_id, 
                                                      ':url_friendly' => $url_piece_single));
                $row = $get_reverse_url_query->fetch(PDO::FETCH_ASSOC);
                
                // Check if anything was returned
                if (isset($row['id'])) {
                    // Check if this is the last element
                    if ($k == ($num_pieces - 1)) {
                        // Last element, just use createById
                        $this->createById($row['id']);
                    }
                    else {
                        // Was found, update the current id
                        $temp_id = $row['id'];
                        
                        // Add url piece
                        $this->fullUrl[] = $url_piece_single;
                        
                        // Check if this object already exists
                        $temp_item = $this->controller->collection->get($temp_id);
                        
                        // Check if already cached, or not
                        if ($temp_item == null) {
                            // Should cache, just in case
                            $temp_item = new Item($this->controller);
                            $temp_item->createById($temp_id);
                            $temp_item->controller->collection->add($temp_item);
                        }

                        // Check if we should add to location array too
                        if ($this->loadFullLocation) {
                            $this->fullLocation[] = $temp_item->getLocation();
                        }
                    }
                }
            }
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
                $temp_item = $this->controller->collection->get($temp_id);
                
                // Check if already cached, or not
                if ($temp_item == null) {
                    // Create new object
                    $temp_item = new Item($this->controller);
                    $temp_item->createById($temp_id);
                    $this->controller->collection->add($temp_item);
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
    // Setters for loading additional information
    //

    public function setLoadFullLocation($b) {
        $this->loadFullLocation = $b;
    }

    public function setLoadFavorite($b) {
        $this->loadFavorite = $b;
    }
    
    public function setLoadFlagCount($b) {
        $this->loadFlagCount = $b;
    }

    //
    // Generate url for this item
    //

    public function generateUrl($path) {
        // Check if the url is already cached!
        if (count($this->fullUrl) == 0) {
            // Store some variables for later
            $temp_url = array($this->urlFriendly);
            $temp_id = $this->parent;
            $temp_root_parent = $this;

            // Loop untill we reach the root
            while ($temp_id != 0) {
                // Check if this object already exists
                $temp_item = $this->controller->collection->get($temp_id);
                
                // Check if already cached
                if ($temp_item == null) {
                    // Create new object
                    $temp_item = new Item($this->controller);
                    $temp_item->createById($temp_id);
                    $temp_item->controller->collection->add($temp_item);
                }

                // Get the url piece
                $temp_url[] = $temp_item->getUrlFriendly();

                // Update id
                $temp_id = $temp_item->getParent();

                if ($temp_item->getId() != 1) {
                    $temp_root_parent = $temp_item;
                }
            }
            
            // Store the root
            $this->rootParent = $temp_root_parent;
            
            // Reverse array
            $temp_url = array_reverse($temp_url);
            
            // Store in real variable
            $this->fullUrl = $temp_url;
        }

        // Return goes here!
        return substr($path, 1) . implode('/', $this->fullUrl) . ($this->directory ? '/' : '');
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
            $temp_item = $this->controller->collection->get($temp_id);
            
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
            
            $get_download_count_query = $this->controller->db->prepare($get_download_count);
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

    public function addDownload() {
        // Check if user is logged in
        if ($this->controller->user != null and $this->controller->user->isLoggedIn()) {
            // User is logged in
            $insert_user_download = "INSERT INTO download (file, ip, agent, user)
            VALUES (:file, :ip, :agent, :user)";
            
            $insert_user_download_query = $this->controller->db->prepare($insert_user_download);
            $insert_user_download_query->execute(array(':file' => $this->id, 
                                                       ':ip' => $_SERVER['REMOTE_ADDR'], 
                                                       ':agent' => $_SERVER['HTTP_USER_AGENT'], 
                                                       ':user' => $this->user->getId()));
        }
        else {
            // Is not logged in
            $insert_anon_download = "INSERT INTO download (file, ip, agent)
            VALUES (:file, :ip, :agent)";
            
            $insert_anon_download_query = $this->controller->db->prepare($insert_anon_download);
            $insert_anon_download_query->execute(array(':file' => $this->id, 
                                                        ':ip' => $_SERVER['REMOTE_ADDR'], 
                                                        ':agent' => $_SERVER['HTTP_USER_AGENT']));
        }
    }

    //
    // Flags TODO
    //

    public function loadFlags() {
        $get_all_flags = "SELECT *
        FROM flag
        WHERE file = :file
        AND active = 1";
        
        $get_all_flags_query = $this->controller->db->prepare($get_all_flags);
        $get_all_flags_query->execute(array(':file' => $this->id));
        while ($row = $get_all_flags_query->fetch(PDO::FETCH_ASSOC)) {
            $this->flags[] = $row;
        }

        $this->flagCount = count($this->flags);

        // Check if we should cache this Item
        if ($this->cache) {
            $this->controller->cacheManager->setCache($id, $this->cacheFormat());
        }
    }

    public function getFlagCount() {
        if ($this->flagCount === false) {
            // Flags are not loaded, load them first
            $this->loadFlags(false);
        }

        return $this->flagCount;
    }

    public function getFlags() {
        if ($this->flagCount === false) {
            // Flags are not loaded, load them first
            $this->loadFlags(true);
        }
        
        return $this->flags;
    }

    //
    // Favorite
    //

    public function isFavorite() {
        // First, check if logged in
        if ($this->controller->user != null and $this->controller->user->isLoggedIn()) {
            // Check if fetched
            if ($this->favorite === null) {
                // Not fetched
                $get_favorite_status = "SELECT id
                FROM favorite
                WHERE file = :file
                AND user = :user";
                
                $get_favorite_status_query = $this->controller->db->prepare($get_favorite_status);
                $get_favorite_status_query->execute(array(':file' => $this->id, 
                                                          ':user' => $this->controller->user->getId()));
                $row = $get_favorite_status_query->fetch(PDO::FETCH_ASSOC);
                
                // Cache for later
                if (isset($row['id'])) {
                    $this->favorite = true;
                }
                else {
                    $this->favorite = false;
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
            return null;
        }
    }

    //
    // Root parent
    //

    public function setShouldLoadRoot($b) {
        $this->shouldLoadRoot = $b;
    }

    public function getRootParent() {
        if ($this->rootParent != null) {
            return $this->rootParent;
        }
        else {
            // Temp variables
            $temp_id = $this->parent;
            $temp_root_parent = $this;

            // Loop untill we reach the root
            while ($temp_id != 0) {
                // Check if this object already exists
                $temp_item = $this->controller->collection->get($temp_id);
                
                // Check if already cached
                if ($temp_item == null) {
                    // Create new object
                    $temp_item = new Item($this->controller);
                    $temp_item->createById($temp_id);
                    $temp_item->controller->collection->add($temp_item);
                }

                // Update id
                $temp_id = $temp_item->getParent();

                if ($temp_item->getId() != 1) {
                    $temp_root_parent = $temp_item;
                }
            }
            
            // Store the root
            $this->rootParent = $temp_root_parent;

            return $this->rootParent;
        }
    }

    //
    // Course
    //

    public function hasCourse() {
        return (($this->course == null) ? false : true);
    }

    public function getCourse() {
        // Check if course object is set
        if ($this->courseObj == null) {
            // Create object and set id
            $this->courseObj = new Course($this->controller);
            $this->courseObj->setId($this->course);
        }

        // Fetch name
        return $this->courseObj;
    }

    //
    // Setter for cache lookup
    //

    public function getCache($b) {
        $this->cache = $b;
    }

    //
    // Create cache string for this Item
    //

    private function cacheFormat() {
        $cache_temp = array();
        $fields = array('name', 'directory', 'urlFriendly', 'parent', 'mimeType', 'missingImage', 'accepted', 
                        'visible', 'location', 'added', 'size', 'course', 'flagCount');
        
        // Loop each field
        foreach ($fields as $v) {
            if ($this->$v != null) {
                $cache_temp[] = "'" . $v . "' => '" . addslashes($this->$v) . "'";
            }
        }

        // Implode and return
        return implode(', ', $cache_temp);
    }
}