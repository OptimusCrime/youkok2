<?php
/*
 * File: ElmentController.php
 * Holds: Controller for the model Element
 * Created: 06.11.2014
 * Project: Youkok2
*/

namespace Youkok2\Models\Controllers;

/*
 * Define what classes to use
 */

use \Youkok2\Collections\ElementCollection as ElementCollection;
use \Youkok2\Models\Element as Element;
use \Youkok2\Shared\Elements as Elements;
use \Youkok2\Models\Course as Course;
use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\CacheManager as CacheManager;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Routes as Routes;
use \Youkok2\Utilities\Utilities as Utilities;

/*
 * The class ElementController
 */

class ElementController implements BaseController {
    
    /*
     * Variables
     */
    
    private $model;
    
    // The query
    private $query;
    
    // Full paths
    private $fullUrl;
    private $finishedLoadingUrl;
    private $fullLocation;
    
    // Load additional information
    private $loadFullLocation;
    private $loadFavorite;
    private $loadFlagCount;
    private $loadRootParent;
    private $loadIfRemoved;
    
    // Other stuff
    private $favorite;
    private $rootParent;
    private $flagCount;
    private $downloadCount;
    
    // Owner
    private $ownerId;
    private $ownerUsername;
    
    // Pointers to other objects related to this item
    private $courseObj;
    private $flags;
    
    // Cache
    private $cache;
    
    // Static options
    public static $file = 0;
    public static $dir = 1;
    
    public static $accumulated = 0;
    public static $single = 1;
    
    public static $delta = array(' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 WEEK) AND a.is_visible = 1', 
                                 ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND a.is_visible = 1', 
                                 ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 YEAR) AND a.is_visible = 1', 
                                 ' WHERE a.is_visible = 1',
                                 ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND a.is_visible = 1');
    
    /*
     * Consutrctor
     */
    
    public function __construct($model) {
        // Set pointer to the model
        $this->model = $model;
        
        // Init array for the query
        $this->query = array('select' => array('a.name', 'a.parent', 'a.is_directory', 'a.url_friendly', 
                                               'a.mime_type', 'a.missing_image', 'a.is_accepted', 'a.is_visible', 
                                               'a.added', 'a.location', 'a.size', 'a.course', 'a.url'), 
                             'join' => array(), 
                             'where' => array('WHERE a.id = :id'),
                             'groupby' => array(),
                             'execute' => array());

        // Variables to keep track of what should be loaded at creation
        $this->loadFullLocation = false;
        $this->loadFavorite = false;
        $this->loadFlagCount = false;
        $this->loadRootParent = false;
        $this->loadIfRemoved = false;

        // Create arrays for full locations
        $this->fullUrl = array();
        $this->fullLocation = array();
        
        // Other stuff
        $this->favorite = null;
        $this->rootParent = null;
        $this->flagCount = null;
        $this->finishedLoadingUrl = true;
        $this->downloadCount = array(0 => null, 
                                     1 => null, 
                                     2 => null, 
                                     3 => null);
        
        // Owner
        $this->ownerId = null;
        $this->ownerUsername = null;
        
        // Set pointers to other objects
        $this->flags = null;

        // Set caching to true as default
        $this->cache = true;
    }
    
    /*
     * Methods for creating the element
     */
    
    public function createById($id) {
        $this->model->setId($id);

        // Check if we should check the cache and if it is cached
        if ($this->cache and CacheManager::isCached($id, 'i')) {
            // This Item is cached, go ahead and fetch data
            $temp_cache_data = CacheManager::getCache($id, 'i');
            // Loop all the fields and apply data
            foreach ($temp_cache_data as $k => $v) {
                $k_actual = 'set' . ucfirst($k);
                // Check that the field exists as a property/attribute in this class
                if (method_exists('\Youkok2\Models\Element', $k_actual)) {
                    // Check if Course
                    if ($k == 'course') {
                         if ($v != null and strlen($v) > 0) {
                            $course_obj = new Course();
                            $course_obj->createById($v);
                            $this->model->setCourse($course_obj);
                         }
                    }
                    else {
                        // Set value
                        call_user_func_array(array($this->model, $k_actual), array($v));
                    }
                }
            }

            // If we are fetching the full location, this should be the last fragment
            if (count($this->fullLocation) > 0 and $this->loadFullLocation) {
                $this->fullLocation[] = $temp_cache_data['location'];
            }

            // Cached flagcount?
            if (isset($temp_cache_data['flagCount'])) {
                $this->flagCount = $temp_cache_data['flagCount'];
            }
        }
        else {
            // Add id to dynamic query
            $this->query['execute'][':id'] = $this->model->getId();
            
            if (!$this->loadIfRemoved) {
                $this->query['where'][] = 'a.is_visible = 1';
            }

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
                $this->query['join'][] = PHP_EOL . 'LEFT JOIN flag as fl on a.id = fl.file AND fl.active = 1';
                $this->query['groupby'][] = 'a.id';
            }
            
            // Create the actual query
            $get_item_info = 'SELECT ' . implode(', ', $this->query['select']) . PHP_EOL . 'FROM archive AS a ';
            
            // Add joins (if there are any)
            if (count($this->query['join']) > 0) {
                $get_item_info .= implode(' ', $this->query['join']);
            }
            
            // Add where
            $get_item_info .= PHP_EOL . implode(' AND ', $this->query['where']);;
            
            // Add group by (again, if there are any)
            if (count($this->query['groupby']) > 0) {
                $get_item_info .= PHP_EOL . 'GROUP BY ' . implode(', ', $this->query['groupby']);
            }
            
            // Run the actual query
            $get_item_info_query = Database::$db->prepare($get_item_info);
            $get_item_info_query->execute($this->query['execute']);
            $row = $get_item_info_query->fetch(\PDO::FETCH_ASSOC);
            
            // Check if the query did return anything
            if (isset($row['name'])) {
                // Set special fields
                if ($this->loadFavorite) {
                    $this->favorite = (!isset($row['favorite']) or $row['favorite'] == null) ? false : true;
                }
                if (isset($row['flags'])) {
                    $this->flagCount = $row['flags'];
                }
                
                // Set results
                $this->model->setName($row['name']);
                $this->model->setDirectory($row['is_directory']);
                $this->model->setUrlFriendly($row['url_friendly']);
                $this->model->setParent($row['parent']);
                $this->model->setMimeType($row['mime_type']);
                $this->model->setMissingImage($row['missing_image']);
                $this->model->setAccepted($row['is_accepted']);
                $this->model->setVisible($row['is_visible']);
                $this->model->setLocation($row['location']);
                $this->model->setAdded($row['added']);
                $this->model->setSize($row['size']);
                $this->model->setUrl($row['url']);
                
                if (isset($row['course']) and $row['course'] != null and strlen($row['course']) > 0) {
                    $course_obj = new Course();
                    $course_obj->createById($row['course']);
                    $this->model->setCourse($course_obj);
                }
                
                // If we are fetching the full location, this should be the last fragment
                if ($this->loadFullLocation) {
                    $this->fullLocation[] = $row['location'];
                }

                // Check if we should cache this Item
                if ($this->cache) {
                    $this->cache();
                }
            }
            else {
                // Was not found
                $this->model->setId(null);
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
                $get_reverse_url  = "SELECT id" . PHP_EOL;
                $get_reverse_url .= "FROM archive " . PHP_EOL;
                $get_reverse_url .= "WHERE parent = :parent" . PHP_EOL;
                $get_reverse_url .= "AND url_friendly = :url_friendly" . PHP_EOL;
                $get_reverse_url .= "AND is_visible = 1";
                
                $get_reverse_url_query = Database::$db->prepare($get_reverse_url);
                $get_reverse_url_query->execute(array(':parent' => $temp_id, 
                                                      ':url_friendly' => $url_piece_single));
                $row = $get_reverse_url_query->fetch(\PDO::FETCH_ASSOC);
                
                // Check if anything was returned
                if (isset($row['id'])) {
                    // Check if we should load root parent
                    if ($this->loadRootParent and $k == 0) {
                        $this->rootParent = $row['id'];
                    }

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
                        $this->finishedLoadingUrl = false;
                        
                        // Check if this object already exists
                        $temp_item = ElementCollection::get($temp_id);
                        
                        // Check if already cached, or not
                        if ($temp_item == null) {
                            // Should cache, just in case
                            $temp_item = new Element();
                            $temp_item->createById($temp_id);
                            ElementCollection::add($temp_item);
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
    
    /*
     * Check if the Element was found or not
     */

    public function wasFound() {
        if ($this->model->getId() != null and is_numeric($this->model->getId())) {
            return true;
        }
        else {
            return false;
        }
    }

    /*
     * Some special getters
     */

    public function getSizePretty() {
        return Utilities::prettifyFilesize($this->size);
    }
    
    /*
     * Returning the full location for the Element
     */

    public function getFullLocation() {
        if (count($this->fullLocation) == 0) {
            $temp_location = array($this->model->getLocation());
            $temp_id = $this->model->getParent();

            // Loop untill we reach the root
            while ($temp_id != 0) {
                // Check if this object already exists
                $temp_item = ElementCollection::get($temp_id);
                
                // Check if already cached, or not
                if ($temp_item !== null) {
                    // Get the url piece
                    $temp_location[] = $temp_item->getLocation();
                    
                    // Update id
                    $temp_id = $temp_item->getParent();
                }
                else {
                    die('500');
                }
            }

            // Reverse array
            $temp_location = array_reverse($temp_location);

            // Assign
            $this->fullLocation = $temp_location;
        }

        return implode('/', $this->fullLocation);
    }

    /*
     * Setters to load additional information when loaded
     */

    public function setLoadFullLocation($b) {
        $this->loadFullLocation = $b;
    }

    public function setLoadFavorite($b) {
        $this->loadFavorite = $b;
    }
    
    public function setLoadFlagCount($b) {
        $this->loadFlagCount = $b;
    }

    public function setLoadRootParent($b) {
        $this->loadRoot = $b;
    }

    public function setLoadIfRemoved($b) {
        $this->loadIfRemoved = $b;
        $this->cache = false;
    }

    /*
     * Generate url for the current Element
     */

    public function generateUrl($path) {
        // Check if the url is already cached!
        if (count($this->fullUrl) == 0 or $this->finishedLoadingUrl == false) {
            // Store some variables for later
            $temp_url= array($this->model->getUrlFriendly());
            $temp_id = $this->model->getParent();
            $temp_root_parent = $this->model;

            // Loop untill we reach the root
            while ($temp_id != 0) {
                // Check if this object already exists
                $temp_item = ElementCollection::get($temp_id);
                
                // Check if already cached
                if ($temp_item == null) {
                    // Create new object
                    $temp_item = new Element();
                    $temp_item->createById($temp_id);
                    ElementCollection::add($temp_item);
                }
                
                // Get the url piece
                $temp_url_friendly = $temp_item->getUrlFriendly();
                if (strlen($temp_url_friendly) > 0) {
                    $temp_url[] = $temp_url_friendly;
                }

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
            
            // Check if we should reset so we don't have to fetch this again
            if (!$this->finishedLoadingUrl) {
                $this->finishedLoadingUrl = true;
            }
        }
        
        // Return goes here!
        if ($this->model->isLink()) {
            return substr($path, 1) . '/' . $this->model->getId();
        }
        else {
            return substr($path, 1) . '/' . implode('/', $this->fullUrl) . ($this->model->isDirectory() ? '/' : '');
        }
    }

    /*
     * Get breadcrumbs for the current Element
     */

    public function getBreadcrumbs() {
        // Store some variables for later
        $temp_collection = array($this);
        $temp_id = $this->parent;
        
        // Loop untill we reach the root
        while ($temp_id != null) {
            // Check if this object already exists
            $temp_item = ElementCollection::get($temp_id);
            
            // Get the url piece
            $temp_collection[] = $temp_item;

            // Update id
            $temp_id = $temp_item->getParent(); 
        }

        // Return breadcrumbs in correct order here
        return array_reverse($temp_collection);
    }

    /*
     * Download methods
     */
    
    public function getDownloadCount($d) {
        // Get the delta index
        $index = 0;
        foreach (Elements::$delta as $k => $v) {
            if ($v == $d) {
                $index = $k;
                break;
            }
        }
        
        // Check if null
        if ($this->downloadCount[$index] == null) {
            // TODO, check what is fetched!

            // This count is not cached, run query to fetch the download number
            $get_download_count  = "SELECT d.file as 'id', COUNT(d.id) as 'downloaded_times'" . PHP_EOL;
            $get_download_count .= "FROM download d" . PHP_EOL;
            $get_download_count .= "LEFT JOIN archive AS a ON a.id = d.file" . PHP_EOL;
            $get_download_count .= $d . PHP_EOL;
            $get_download_count .= "AND d.file = :file";
            
            $get_download_count_query = $this->controller->db->prepare($get_download_count);
            $get_download_count_query->execute(array(':file' => $this->id));
            $row = $get_download_count_query->fetch(PDO::FETCH_ASSOC);

            // Set value
            $this->downloadCount[$index] = $row['downloaded_times'];

            // Return value
            return $row['downloaded_times'];
        }
        else {
            // Cached result, just return
            return $this->downloadCount[$index];
        }
    }
    
    public function setDownloadCount($delta, $value) {
        $this->downloadCount[$delta] = $value;
    }

    public function addDownload() {
        // Check if user is logged in
        if ($this->controller->user != null and $this->controller->user->isLoggedIn()) {
            // User is logged in
            $insert_user_download  = "INSERT INTO download (file, ip, agent, user)" . PHP_EOL;
            $insert_user_download .= "VALUES (:file, :ip, :agent, :user)";
            
            $insert_user_download_query = $this->controller->db->prepare($insert_user_download);
            $insert_user_download_query->execute(array(':file' => $this->id, 
                                                       ':ip' => $_SERVER['REMOTE_ADDR'], 
                                                       ':agent' => $_SERVER['HTTP_USER_AGENT'], 
                                                       ':user' => $this->controller->user->getId()));
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

    /*
     * Flags
     */

    public function loadFlags() {
        $get_all_flags  = "SELECT *" . PHP_EOL;
        $get_all_flags .= "FROM flag" . PHP_EOL;
        $get_all_flags .= "WHERE file = :file" . PHP_EOL;
        $get_all_flags .= "AND active = 1";
        
        $get_all_flags_query = $this->controller->db->prepare($get_all_flags);
        $get_all_flags_query->execute(array(':file' => $this->id));
        while ($row = $get_all_flags_query->fetch(PDO::FETCH_ASSOC)) {
            // Create new flag
            $flag = new Flag($this->controller);

            // Set all fields
            $flag->setAll($row);

            // Add object to array
            $this->flags[] = $flag;
        }

        $this->flagCount = count($this->flags);

        // Check if we should cache this Item
        if ($this->cache) {
            $this->controller->cacheManager->setCache($this->id, 'i', $this->cacheFormat());
        }
    }

    public function getFlagCount() {
        return 1;
        if ($this->flagCount === null) {
            // Flags are not loaded, load them first
            $this->loadFlags();
        }

        return $this->flagCount;
    }

    public function getFlags() {
        if ($this->flags === null) {
            // Flags are not loaded, load them first
            $this->flags = array();
            $this->loadFlags();
        }
        
        return $this->flags;
    }

    /*
     * Favorite
     */

    public function isFavorite() {
        return false;
        // TODO
        // First, check if logged in
        if (Me::isLoggedIn()) {
            // Check if fetched
            if ($this->favorite === null) {
                // Not fetched
                $get_favorite_status  = "SELECT id" . PHP_EOL;
                $get_favorite_status .= "FROM favorite" . PHP_EOL;
                $get_favorite_status .= "WHERE file = :file" . PHP_EOL;
                $get_favorite_status .= "AND user = :user";
                
                $get_favorite_status_query = Database::$db->prepare($get_favorite_status);
                $get_favorite_status_query->execute(array(':file' => $this->model->getId(), 
                                                          ':user' => Me::getId()));
                $row = $get_favorite_status_query->fetch(\PDO::FETCH_ASSOC);
                
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

    /*
     * Root parent
     */

    public function getRootParent() {
        if ($this->rootParent != null) {
            return $this->rootParent;
        }
        else {
            // Temp variables
            $temp_id = $this->model->getParent();
            $temp_root_parent = $this->model;

            // Loop untill we reach the root
            while ($temp_id != 0) {
                // Check if this object already exists
                $temp_element = ElementCollection::get($temp_id);
                
                // Check if already cached
                if ($temp_element !== null) {
                    // Create new object
                    $temp_element = new Element();
                    $temp_element->createById($temp_id);
                    $temp_id = $temp_element->getParent();

                    if ($temp_element->getId() != 1) {
                        $temp_root_parent = $temp_element;
                    }
                }
                else {
                    die('500');
                }
            }
            
            // Store the root
            $this->rootParent = $temp_root_parent;

            return $this->rootParent;
        }
    }
    
    /*
     * Course
     */

    public function hasCourse() {
        return (($this->model->getCourse() == null) ? false : true);
    }

    public function setCourseFromId($id) {
        $course_obj = new Course();
        $course_obj->createById($id);
        $this->model->setCourse($course_obj);
    }
    
    /*
     * Setter for caching
     */

    public function getCache($b) {
        $this->cache = $b;
    }

    /*
     * Create cache string for this Element
     */

    private function cacheFormat() {
        $cache_temp = array();
        $fields = array('getId', 'getName', 'isDirectory', 'getUrlFriendly', 'getParent', 'getMimeType', 'getMissingImage', 
                        'isAccepted', 'isVisible', 'getLocation', 'getAdded', 'getSize', 'getCourse', 'getUrl');
        
        // Loop each field
        foreach ($fields as $v) {
            if (substr($v, 0, 3) == 'get') {
                $v_pretty = strtolower(substr($v, 3));
            }
            else {
                $v_pretty = strtolower(substr($v, 2));
            }
            
            if (method_exists('\Youkok2\Models\Element', $v)) {
                if ($v == 'getCourse' and $this->hasCourse() == true) {
                    $cache_temp[] = "'" . $v_pretty . "' => '" . addslashes($this->model->getCourse()->getId()) . "'";
                }
                else {
                    $cache_temp[] = "'" . $v_pretty . "' => '" . addslashes(call_user_func(array($this->model, $v))) . "'";
                }
            }
        }
        
        // Add flag count
         $cache_temp[] = "'flagcount' => '" . addslashes($this->getFlagCount()) . "'";
         
        // Implode and return
        return implode(', ', $cache_temp);
    }
    
    /*
     * Get direct children
     */
    
    public function getChildren($flag = null) {
        $children = array();
        $subquery = '';
        
        if ($flag !== null) {
            $subquery = ' AND is_directory = ' . $flag;
        }
        
        // Load all favorites
        $get_children_ids  = "SELECT id" . PHP_EOL;
        $get_children_ids .= "FROM archive" . PHP_EOL;
        $get_children_ids .= "WHERE parent = :parent" . $subquery;
        
        $get_children_ids_query = $this->controller->db->prepare($get_children_ids);
        $get_children_ids_query->execute(array(':parent' => $this->id));
        while ($row = $get_children_ids_query->fetch(PDO::FETCH_ASSOC)) {
            $element = new Item($this->controller);
            $element->setLoadFullLocation(true);
            $element->createById($row['id']);
            $this->controller->collection->add($element);
            $children[] = $element;
        }
        
        return $children;
    }
    
    public function getChildrenCount($flag = null) {
        return count($this->getChildren($flag));
    }
    
    /*
     * Get creator
     */
    
    public function getOwnerId() {
        // Only fetch if null
        if ($this->ownerId == null) {
            // Just call getOwnerUsername, to make things easier
            $this->getOwnerUsername();
        }
        
        // Return
        return $this->ownerId;
    }
    
    public function getOwnerUsername() {
        // Fetch only if null
        if ($this->ownerUsername == null) {
            $get_owner  = "SELECT u.id, u.nick" . PHP_EOL;
            $get_owner .= "FROM user AS u" . PHP_EOL;
            $get_owner .= "LEFT JOIN flag AS f ON f.user = u.id" . PHP_EOL;
            $get_owner .= "WHERE f.file = :file" . PHP_EOL;
            $get_owner .= "AND f.type = 0" . PHP_EOL;
            $get_owner .= "LIMIT 1";
            
            $get_owner_query = $this->controller->db->prepare($get_owner);
            $get_owner_query->execute(array(':file' => $this->id));
            $row = $get_owner_query->fetch(PDO::FETCH_ASSOC);
            
            if (isset($row['id'])) {
                $this->ownerId = $row['id'];
                
                if (!isset($row['nick']) or $row['nick'] == '') {
                    $this->ownerUsername = '<em>Anonym</em>';
                }
                else {
                    $this->ownerUsername = $row['nick'];
                }
            }
        }
        
        // Return here
        return $this->ownerUsername;
    }
    
    /*
     * Get graph data
     */
    
    public function getGraphData($type = null) {
        // Set type if null
        if ($type === null) {
            $type = 0;
        }
        
        // Some variables
        $output = array();
        $previous_num = 0;
        
        // The query
        $get_all_downloads = "SELECT COUNT(id) AS 'num', downloaded_time" . PHP_EOL;
        $get_all_downloads .= "FROM download" . PHP_EOL;
        $get_all_downloads .= "WHERE file = :file" . PHP_EOL;
        $get_all_downloads .= "GROUP BY TO_DAYS(downloaded_time)" . PHP_EOL;
        $get_all_downloads .= "ORDER BY downloaded_time ASC";
        
        $get_all_downloads_query = $this->controller->db->prepare($get_all_downloads);
        $get_all_downloads_query->execute(array(':file' => $this->id));
        while ($row = $get_all_downloads_query->fetch(PDO::FETCH_ASSOC)) {
            // Define how to count downloads
            if ($type == Item::$accumulated) {
                $previous_num += $row['num'];
                $num_count = $previous_num;
            }
            else {
                $num_count = $row['num'];
            }
            
            // Split the timestamp
            $ts_split = explode(' ', $row['downloaded_time']);
            $date_split = explode('-', $ts_split[0]);
            $time_split = explode(':', $ts_split[1]);
            
            // The string for Higcharts
            $output[] = array('Date.UTC(' . $date_split[0] . ', ' . $date_split[1] . ', ' . $date_split[2] . ', ' . $time_split[0] . ', ' . $time_split[1] . ', ' . $time_split[2] . ')',
                              $num_count);
        }
        
        // Return the series here
        return json_encode($output);
    }
    
    /*
     * Generate frontpage link for the current Element
     */
    
    public function getFrontpageLink($mode = null, $special = null) {
        // Some variable we are going to eed
        $ret = '';
        $endfix = '';
        $list_classes = 'list-group-item';
        $root_parent = $this->getRootParent();
        
        // Check if we should load additional information
        if ($mode != null) {
            if ($mode == 'added') {
                $endfix .= ' [<span class="moment-timestamp help" data-toggle="tooltip" title="' . Utilities::prettifySQLDate($this->model->getAdded()) . '" ';
                $endfix .= 'data-ts="' . $this->model->getAdded() . '">Laster...</span>]';
            }
            else if ($mode == 'most-popular') {
                $endfix .= ' [' . number_format($this->getDownloadCount(Elements::$delta[Me::getUserDelta($special)])) . ']';
            }
            else if ($mode == 'favorites') {
                $list_classes .= ' list-group-star';
                $endfix = '    <i title="Fjern favoritt" data-id="' . $this->model->getId() . '" class="fa fa-times-circle star-remove"></i>';
            }
        }
        
        // The different types of Elements requires different links
        if ($this->model->isLink()) {
            $element_url = $this->generateUrl(Routes::REDIRECT);
            $element_title = ' data-toggle="tooltip" class="help" title="Link til: ' . $this->model->getUrl() . '"';
        }
        else if ($this->model->isFile()) {
            $element_url = $this->generateUrl(Routes::DOWNLOAD);
            $element_title = '';
        }
        
        // Begin the list
        $ret .= '<li class="' . $list_classes . '">' . PHP_EOL;
        
        // Check if directory
        if ($this->model->isDirectory()) {
            $ret .= '    <a href="' . $this->generateUrl(Routes::ARCHIVE) . '">' . $this->model->getName();
            
            // Check if has course
            if ($this->hasCourse()) {
                $ret .= ' &mdash; ' . $this->model->getCourse()->getName();
            }
            
            // Close link
            $ret .= '</a>' . PHP_EOL;
        }
        else {
            // The link itself
            $ret .= '    <a rel="nofollow" target="_blank"' . $element_title . ' href="' . $element_url . '">' . $this->model->getName() . '</a> @ ' . PHP_EOL;
            
            // Check if we should output the parent
            if ($this->model->getParent() != 1 and $this->model->getParent() != $root_parent->getId()) {
                $local_dir_element = ElementCollection::get($this->model->getParent());
                $ret .= '    <a href="' . $this->generateUrl(Routes::ARCHIVE) . '">' . $local_dir_element->getName() . '</a>, ' . PHP_EOL;
            }
            
            // Check if we should add the root parent
            if ($root_parent != null) {
                $ret .= '    <a href="' . $root_parent->controller->generateUrl(Routes::ARCHIVE) . '" data-toggle="tooltip" ';
                $ret .= ' data-placement="top" class="help" title="' . $root_parent->getCourse()->getName() . '">' . $root_parent->getName() . '</a>' . PHP_EOL;
            }
        }
        
        // Close the list
        $ret .= $endfix . PHP_EOL;
        $ret .= '</li>' . PHP_EOL;
        
        // Return the entire string
        return $ret;
    }
    
    /*
     * Cache the current Element
     */
    
    public function cache() {
        CacheManager::setCache($this->model->getId(), 'i', $this->cacheFormat());
    }
    
    /*
     * Save the current Element
     */
    
    public function save() {
        $insert_element  = "INSERT INTO archive (name, url_friendly, parent, course, location, mime_type, missing_image, size, is_directory, is_accepted, is_visible, url, added) " . PHP_EOL;
        $insert_element .= "VALUES (:name, :url_friendly, :parent, :course, :location, :mime_type, :missing_image, :size, :is_directory, :is_accepted, :is_visible, :url, NOW())";

        $insert_element_query = Database::$db->prepare($insert_element);
        $insert_element_query->execute([':name' => $this->model->getName(),
            ':url_friendly' => $this->model->getUrlFriendly(),
            ':parent' => $this->model->getParent(),
            ':course' => (($this->model->getCourse() === null) ? null : $this->model->getCourse()->getId()),
            ':location' => $this->model->getLocation(),
            ':mime_type' => $this->model->getMimeType(),
            ':missing_image' => (int) $this->model->getMissingImage(),
            ':size' => $this->model->getSize(),
            ':is_directory' => (int) $this->model->isDirectory(),
            ':is_accepted' => (int) $this->model->isAccepted(),
            ':is_visible' => (int) $this->model->isVisible(),
            ':url' => $this->model->getUrl(),
        ]);

        // Get the course-id
        $element_id = Database::$db->lastInsertId();
        
        // Set id to model
        $this->model->setId($element_id);
        
        // Cache
        $this->cache();
    }
    
    /*
     * Update the current Element
     */
    
    public function update() {
        // TODO
    }
}