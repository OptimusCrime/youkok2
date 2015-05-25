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
    
    // Load additional information
    private $loadFlagCount;
    private $loadIfRemoved;
    
    // Other stuff
    private $flagCount;
    private $downloadCount;
    
    // Owner
    private $ownerId;
    private $ownerUsername;
    
    // Pointers to other objects related to this item
    private $flags;
    
    // Cache
    private $cache;
    
    // Parents and children
    private $parents;
    private $children;
    
    // Static options
    public static $file = 0;
    public static $dir = 1;
    
    public static $accumulated = 0;
    public static $single = 1;
    
    public static $delta = array('WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 WEEK) AND a.is_visible = 1', 
        'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND a.is_visible = 1', 
        'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 YEAR) AND a.is_visible = 1', 
        'WHERE a.is_visible = 1',
        'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND a.is_visible = 1');
    
    /*
     * Consutrctor
     */
    
    public function __construct($model) {
        // Set pointer to the model
        $this->model = $model;
        
        // Init array for the query
        $this->query = array('select' => array('a.name', 'a.parent', 'a.empty', 'a.checksum', 'a.is_directory', 
                'a.url_friendly', 'a.mime_type', 'a.missing_image', 'a.is_accepted', 'a.is_visible', 
                'a.added', 'a.size', 'a.exam', 'a.url'), 
            'join' => array(), 
            'where' => array('WHERE a.id = :id'),
            'groupby' => array(),
            'execute' => array());

        // Variables to keep track of what should be loaded at creation
        $this->loadFlagCount = false;
        $this->loadIfRemoved = false;

        // Other stuff
        $this->flagCount = null;
        $this->downloadCount = array(0 => null, 1 => null, 2 => null, 3 => null);
        
        // Owner
        $this->ownerId = null;
        $this->ownerUsername = null;
        
        // Set pointers to other objects
        $this->flags = null;

        // Set caching to true as default
        $this->cache = true;
        
        // Parents and children
        $this->parents = null;
        $this->children = null;
    }
    
    /*
     * Methods for creating the element
     */
    
    public function createById($id, $skip_db = false) {
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
                    // Only call if value is not empty, instead use the default values
                    if (strlen($v) != 0) {
                        call_user_func_array(array($this->model, $k_actual), array($v));
                    }
                }
            }

            // Cached flagcount?
            if (isset($temp_cache_data['flagCount'])) {
                $this->flagCount = $temp_cache_data['flagCount'];
            }
        }
        else {
            if (!$skip_db) {
                // Add id to dynamic query
                $this->query['execute'][':id'] = $this->model->getId();
                
                if (!$this->loadIfRemoved) {
                    $this->query['where'][] = 'a.is_visible = 1';
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
                    if (isset($row['flags'])) {
                        $this->flagCount = $row['flags'];
                    }
                    
                    // Set results
                    $this->model->setName($row['name']);
                    $this->model->setDirectory($row['is_directory']);
                    $this->model->setUrlFriendly($row['url_friendly']);
                    $this->model->setParent($row['parent']);
                    $this->model->setEmpty((($row['empty'] == '0') ? false : true));
                    $this->model->setChecksum($row['checksum']);
                    $this->model->setMimeType($row['mime_type']);
                    $this->model->setMissingImage($row['missing_image']);
                    $this->model->setAccepted((($row['is_accepted'] == '0') ? false : true));
                    $this->model->setVisible((($row['is_visible'] == '0') ? false : true));
                    $this->model->setAdded($row['added']);
                    $this->model->setSize($row['size']);
                    $this->model->setExam($row['exam']);
                    $this->model->setUrl($row['url']);

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
            $temp_parent = null;
            
            // Loop each fragment
            foreach ($url_pieces as $k => $url_piece_single) {
                // Run query for this fragment
                $get_reverse_url  = "SELECT id" . PHP_EOL;
                $get_reverse_url .= "FROM archive " . PHP_EOL;
                
                if ($temp_parent === null) {
                    $get_reverse_url .= "WHERE parent IS NULL" . PHP_EOL;
                    $reverse_arr = array(':url_friendly' => $url_piece_single);
                }
                else {
                    $get_reverse_url .= "WHERE parent = :parent" . PHP_EOL;
                    $reverse_arr = array(':parent' => $temp_parent, 
                        ':url_friendly' => $url_piece_single);
                }
                
                $get_reverse_url .= "AND url_friendly = :url_friendly" . PHP_EOL;
                $get_reverse_url .= "AND is_visible = 1";
                
                $get_reverse_url_query = Database::$db->prepare($get_reverse_url);
                $get_reverse_url_query->execute($reverse_arr);
                $row = $get_reverse_url_query->fetch(\PDO::FETCH_ASSOC);
                
                // Check if anything was returned
                if (isset($row['id'])) {
                    // Check if this is the last element
                    if ($k == ($num_pieces - 1)) {
                        // Last element, just use createById
                        $this->createById($row['id']);
                    }
                    else {
                        // Was found, update the current id
                        $temp_parent = $row['id'];
                        
                        // Check if this object already exists
                        $temp_item = ElementCollection::get($temp_parent);
                        
                        // Check if already cached, or not
                        if ($temp_item == null) {
                            // Should cache, just in case
                            $temp_item = new Element();
                            $temp_item->createById($temp_parent);
                            ElementCollection::add($temp_item);
                        }
                    }
                }
            }
        }
    }
        
    /*
     * Setters to load additional information when loaded
     */
     
    public function setLoadFlagCount($b) {
        $this->loadFlagCount = $b;
    }

    public function setLoadIfRemoved($b) {
        $this->loadIfRemoved = $b;
        $this->cache = false;
    }
    
    /*
     * Check if the Element was found or not
     */

    public function wasFound() {
        if ($this->model->getId() != null and is_numeric($this->model->getId()) and $this->model->isVisible()) {
            return true;
        }
        else {
            return false;
        }
    }
    
    /*
     * Generic method for storing all the parents for a given resource
     */
    
    public function getParents() {
        if ($this->parents === null) {
            $this->parents = array($this->model);
            $temp_parent_id = $this->model->getParent();
            
            // Loop untill we reach the root
            while ($temp_parent_id != null) {
                // Check if this object already exists
                $temp_parent = ElementCollection::get($temp_parent_id);
                
                // Check if already cached
                if ($temp_parent == null) {
                    // Create new object
                    $temp_parent = new Element();
                    $temp_parent->createById($temp_parent_id);
                    ElementCollection::add($temp_parent);
                }
                
                // Add node to parents
                $this->parents[] = $temp_parent;
                
                // Set new parent id
                $temp_parent_id = $temp_parent->getParent();
            }
            
            // Reverse
            $this->parents = array_reverse($this->parents);
        }
        
        // Return array
        return $this->parents;
    }
    
    /*
     * Get children
     */
    
    public function getChildren() {
        if ($this->children === null) {
            // Load all favorites
            $get_children_ids  = "SELECT id" . PHP_EOL;
            $get_children_ids .= "FROM archive" . PHP_EOL;
            $get_children_ids .= "WHERE parent = :parent" . PHP_EOL;
            $get_children_ids .= "AND is_visible = 1" . PHP_EOL;
            $get_children_ids .= "ORDER BY is_directory DESC, name ASC";
            
            $get_children_ids_query = Database::$db->prepare($get_children_ids);
            $get_children_ids_query->execute(array(':parent' => $this->model->getId()));
            while ($row = $get_children_ids_query->fetch(\PDO::FETCH_ASSOC)) {
                // Create new element
                $element = ElementCollection::get($row['id'], array('flag'));
                
                // Add if found
                if ($element != null and $element->controller->wasFound()) {
                    $this->children[] = $element;
                }
            }
        }
        
        return $this->children;
    }
    
    /*
     * Check if element is course
     */
    
    public function isCourse() {
        return strpos($this->model->getName(), '||') !== false;
    }
    
    /*
     * Return course from element
     */
    
    public function getCourse() {
        $course = $this->model->getName();
        $course_split = explode('||', $course);
        return array('code' => $course_split[0], 'name' => $course_split[1]);
    }

    /*
     * Generate url for the current Element
     */

    public function generateUrl($path) {
        // If generating url for a link, we don't need to fetch the parents
        if ($this->model->isLink()) {
            return substr($path, 1) . '/' . $this->model->getId();
        }
        
        // Check if we should load parents
        if ($this->parents === null) {
            // Load parents first
            $this->getParents();
        }
        
        // Loop the parents and build the url
        $full_url = array();
        foreach ($this->parents as $v) {
            $full_url[] = $v->getUrlFriendly();
        }
        
        // Return goes here!
        return substr($path, 1) . '/' . implode('/', $full_url) . ($this->model->isDirectory() ? '/' : '');
    }

    /*
     * Download methods
     */
    
    public function getDownloadCount($d) {
        // Get the delta index
        $index = 0;
        foreach (self::$delta as $k => $v) {
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
        if (Me::isLoggedIn()) {
            // User is logged in
            $insert_user_download  = "INSERT INTO download (file, ip, agent, user)" . PHP_EOL;
            $insert_user_download .= "VALUES (:file, :ip, :agent, :user)";
            
            $insert_user_download_query = Database::$db->prepare($insert_user_download);
            $insert_user_download_query->execute(array(':file' => $this->model->getId(), 
               ':ip' => $_SERVER['REMOTE_ADDR'], 
               ':agent' => $_SERVER['HTTP_USER_AGENT'], 
               ':user' => Me::getId()));
        }
        else {
            // Is not logged in
            $insert_anon_download  = "INSERT INTO download (file, ip, agent)" . PHP_EOL;
            $insert_anon_download .= "VALUES (:file, :ip, :agent)";
            
            $insert_anon_download_query = Database::$db->prepare($insert_anon_download);
            $insert_anon_download_query->execute(array(':file' => $this->model->getId(), 
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
     * Root parent
     */

    public function getRootParent() {
        // Check if we should load parents
        if ($this->parents === null) {
            // Load parents first
            $this->getParents();
        }
        
        // Return first element in the stack
        return $this->parents[0];
    }
    
    /*
     * Setter for caching
     */

    public function setCache($b) {
        $this->cache = $b;
    }
    
    /*
     * Cache the current Element
     */
    
    public function cache() {
        CacheManager::setCache($this->model->getId(), 'i', $this->cacheFormat());
    }

    /*
     * Create cache string for this Element
     */

    public function cacheFormat() {
        $cache_temp = array();
        $fields = array('getId', 'getName', 'isDirectory', 'getUrlFriendly', 'getParent', 'isEmpty', 'getChecksum',
            'getMimeType', 'getMissingImage', 'isAccepted', 'isVisible', 'getAdded', 'getSize', 
            'getCourse', 'getExam', 'getUrl');
        
        // Loop each field
        foreach ($fields as $v) {
            // Rename cache call fuction
            if (substr($v, 0, 3) == 'get') {
                $v_pretty = strtolower(substr($v, 3));
            }
            else {
                $v_pretty = strtolower(substr($v, 2));
            }
            
            
            if (method_exists('\Youkok2\Models\Element', $v)) {
                // Get the value
                $val = call_user_func(array($this->model, $v));
                
                // Check if we should wrap in quotes
                $wrap = true;
                if (is_bool($val) or is_null($val)) {
                    $wrap = false;
                    
                    if (is_bool($val)) {
                        $val = (int) $val;
                    }
                    if (is_null($val)) {
                        $val = 'null';
                    }
                }
                
                // Add to cache array
                if ($wrap) {
                    $cache_temp[] = "'" . $v_pretty . "' => '" . addslashes($val) . "'";
                }
                else {
                    $cache_temp[] = "'" . $v_pretty . "' => " . $val;
                }
            }
        }
        
        // Add flag count
         $cache_temp[] = "'flagcount' => '" . addslashes($this->getFlagCount()) . "'";
         
        // Implode and return
        return implode(', ', $cache_temp);
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
        $get_all_downloads  = "SELECT COUNT(id) AS 'num', downloaded_time" . PHP_EOL;
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
                // Supply endfix
                $endfix .= ' [' . number_format($this->getDownloadCount(self::$delta[Me::getMostPopularDelta()])) . ']';
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
            $ret .= '    <a href="' . $this->generateUrl(Routes::ARCHIVE) . '">';
            
            // Check if has course
            if ($this->isCourse()) {
                $course = $this->getCourse();
                $ret .= $course['code'] . ' &mdash; ' . $course['name'];
            }
            else {
                $ret .= $this->model->getName();
            }
            
            // Close link
            $ret .= '</a>' . PHP_EOL;
        }
        else {
            // The link itself
            $ret .= '    <a rel="nofollow" target="_blank"' . $element_title . ' href="' . $element_url . '">' . $this->model->getName() . '</a> @ ' . PHP_EOL;
            
            // Load parent
            $parent = ElementCollection::get($this->model->getParent());
            
            // Just to be sure
            if ($parent != null) {
                // Check if element is placed directly in course
                if ($parent->controller->isCourse()) {
                    // Parent is course
                    $parent_course = $parent->controller->getCourse();
                    $ret .= '    <a href="' . $parent->controller->generateUrl(Routes::ARCHIVE) . '" data-toggle="tooltip" ';
                    $ret .= ' data-placement="top" class="help" title="' . $parent_course['name'] . '">' . $parent_course['code'] . '</a>' . PHP_EOL;
                }
                else {
                    // Parent is not course
                    $ret .= '    <a href="' . $parent->controller->generateUrl(Routes::ARCHIVE) . '">' . $parent->getName() . '</a>, ' . PHP_EOL;
                    
                    // Load root parent
                    $parent_course = $root_parent->controller->getCourse();
                    $ret .= '    <a href="' . $root_parent->controller->generateUrl(Routes::ARCHIVE) . '" data-toggle="tooltip" ';
                    $ret .= ' data-placement="top" class="help" title="' . $parent_course['name'] . '">' . $parent_course['code'] . '</a>' . PHP_EOL;
                }
            }
        }
        
        // Close the list
        $ret .= $endfix . PHP_EOL;
        $ret .= '</li>' . PHP_EOL;
        
        // Return the entire string
        return $ret;
    }
    
    public function getPhysicalLocation() {
        // Get directories
        $checksum = $this->model->getChecksum();
        $folder1 = substr($checksum, 0, 1);
        $folder2 = substr($checksum, 1, 1);
        
        // Return full path
        return FILE_PATH . '/' . $folder1 . '/' . $folder2 . '/' . $this->model->getChecksum();
    }
    
    /*
     * Delete cache
     */
    
    public function deleteCache() {
        CacheManager::deleteCache($this->model->getId(), 'i');
    }
    
    /*
     * Save the current Element
     */
    
    public function save() {
        $insert_element  = "INSERT INTO archive (name, url_friendly, owner, parent, empty, checksum, mime_type, missing_image, size, is_directory, is_accepted, is_visible, exam, url, added) " . PHP_EOL;
        $insert_element .= "VALUES (:name, :url_friendly, :owner, :parent, :empty, :checksum, :mime_type, :missing_image, :size, :is_directory, :is_accepted, :is_visible, :exam, :url, NOW())";

        $insert_element_query = Database::$db->prepare($insert_element);
        $insert_element_query->execute([':name' => $this->model->getName(),
            ':url_friendly' => $this->model->getUrlFriendly(),
            ':owner' => $this->model->getOwner(),
            ':parent' => $this->model->getParent(),
            ':empty' => (int) $this->model->isEmpty(),
            ':checksum' => $this->model->getChecksum(),
            ':mime_type' => $this->model->getMimeType(),
            ':missing_image' => (int) $this->model->getMissingImage(),
            ':size' => $this->model->getSize(),
            ':is_directory' => (int) $this->model->isDirectory(),
            ':is_accepted' => (int) $this->model->isAccepted(),
            ':is_visible' => (int) $this->model->isVisible(),
            ':exam' => $this->model->getExam(),
            ':url' => $this->model->getUrl(),
        ]);

        // Set id to model
        $this->model->setId(Database::$db->lastInsertId());
        
        // Cache
        $this->cache();
    }
    
    /*
     * Update the current Element
     */
    
    public function update() {
        try {
            $update_element  = "UPDATE archive SET" . PHP_EOL;
            $update_element .= "name = :name," . PHP_EOL;
            $update_element .= "url_friendly = :url_friendly," . PHP_EOL;
            $update_element .= "owner = :owner," . PHP_EOL;
            $update_element .= "parent = :parent," . PHP_EOL;
            $update_element .= "empty = :empty," . PHP_EOL;
            $update_element .= "checksum = :checksum," . PHP_EOL;
            $update_element .= "mime_type = :mime_type," . PHP_EOL;
            $update_element .= "missing_image = :missing_image," . PHP_EOL;
            $update_element .= "size = :size," . PHP_EOL;
            $update_element .= "is_directory = :is_directory," . PHP_EOL;
            $update_element .= "is_accepted = :is_accepted," . PHP_EOL;
            $update_element .= "is_visible = :is_visible," . PHP_EOL;
            $update_element .= "exam = :exam," . PHP_EOL;
            $update_element .= "url = :url" . PHP_EOL;
            $update_element .= "WHERE id = :id" . PHP_EOL;
            $update_element .= "LIMIT 1";
            
            $update_element_query = Database::$db->prepare($update_element);
            $update_element_query->execute([':name' => $this->model->getName(),
                ':url_friendly' => $this->model->getUrlFriendly(),
                ':owner' => $this->model->getOwner(),
                ':parent' => $this->model->getParent(),
                ':empty' => (int) $this->model->isEmpty(),
                ':checksum' => $this->model->getChecksum(),
                ':mime_type' => $this->model->getMimeType(),
                ':missing_image' => (int) $this->model->getMissingImage(),
                ':size' => $this->model->getSize(),
                ':is_directory' => (int) $this->model->isDirectory(),
                ':is_accepted' => (int) $this->model->isAccepted(),
                ':is_visible' => (int) $this->model->isVisible(),
                ':exam' => $this->model->getExam(),
                ':url' => $this->model->getUrl(),
                ':id' => $this->model->getId(),
            ]);
        }
        catch (\PDOException  $e) {
            print_r($e->getMessage());
            die();
        }
        
        // Cache
        $this->cache();
    }
}