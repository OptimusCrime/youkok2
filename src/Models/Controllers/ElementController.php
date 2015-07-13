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

use \Youkok2\Models\Element as Element;
use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\CacheManager as CacheManager;
use \Youkok2\Utilities\Database as Database;

/*
 * The class ElementController
 */

class ElementController extends BaseController {
    
    /*
     * Variables
     */
    

    public static $cacheKey = 'i';

    // Additional fields in cache
    private $parent;
    private $rootParent;
    private $fullUrl;
    private $parents;


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
        parent::__construct($this, $model);

        // Parents and children
        $this->parents = null;

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
        

        $this->children = null;
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
                        $temp_item = Element::get($temp_parent);
                    }
                }
            }
        }
    }

    /*
     * Check if was found
     */

    public function wasFound() {
        if ($this->model->getId() != null and is_numeric($this->model->getId()) and $this->model->isVisible()) {
            return true;
        }

        return false;
    }
    
    /*
     * Get children
     */
    
    public function getChildren() {
        // Check if the children collection is null
        if ($this->children === null) {
            // Set children to array
            $this->children = [];

            // Load all favorites
            $get_children_ids  = "SELECT id" . PHP_EOL;
            $get_children_ids .= "FROM archive" . PHP_EOL;
            $get_children_ids .= "WHERE parent = :parent" . PHP_EOL;
            $get_children_ids .= "AND is_visible = 1" . PHP_EOL;
            $get_children_ids .= "ORDER BY is_directory DESC, name ASC";
            
            $get_children_ids_query = Database::$db->prepare($get_children_ids);
            $get_children_ids_query->execute(array(':parent' => $this->model->getId()));
            while ($row = $get_children_ids_query->fetch(\PDO::FETCH_ASSOC)) {
                // Get element and add to collection
                $this->children[] = Element::get($row['id']);
            }
        }

        // Return the children collection
        return $this->children;
    }

    /*
     * Generate url for the current Element
     */

    public function generateUrl($path) {
        return $this->getFullUrl($path);
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
            
            $get_download_count_query = Database::$db->prepare($get_download_count);
            $get_download_count_query->execute(array(':file' => $this->model->getId()));
            $row = $get_download_count_query->fetch(\PDO::FETCH_ASSOC);

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
    
    public function getPhysicalLocation() {
        // Get directories
        $checksum = $this->model->getChecksum();
        $folder1 = substr($checksum, 0, 1);
        $folder2 = substr($checksum, 1, 1);
        
        // Return full path
        return FILE_PATH . '/' . $folder1 . '/' . $folder2 . '/' . $this->model->getChecksum();
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

    /*
     * To Array (for output)
     */

    public function toArray() {
        // Get the initial fields from the array
        $arr = $this->model->toArrayInitial();

        // Return the array
        return $arr;
    }

    /*
     * Various helper methods
     */

    public function hasParent() {
        return $this->model->getParent() != null and $this->model->getParent() != 1;
    }
    public function isLink() {
        return ($this->model->getUrl() != null);
    }
    public function isFile() {
        return ($this->model->getUrl() == null and !$this->model->isDirectory());
    }
    public function getParentObject() {
        if ($this->parent === null) {
            $this->parent = Element::get($this->model->getParent());
        }

        return $this->parent;
    }
    public function getParents() {
        $arr = [$this->model];

        if ($this->hasParent()) {
            $temp_obj = $this->getParentObject();
            while (true) {
                // Derp
                $arr[] = $temp_obj;

                if ($temp_obj->hasParent()) {
                    $temp_obj = $temp_obj->getParentObject();
                }
                else {
                    break;
                }

            }
        }

        // Return collection of parents
        return $arr;
    }
    public function getRootParent() {
        if ($this->rootParent === null) {

            $temp_element = $this->model;

            while (true) {
                if ($temp_element->hasParent()) {
                    $temp_element = $temp_element->getParent(true);
                }
                else {
                    $this->rootParent = &$temp_element;
                    break;
                }
            }
        }

        return $this->rootParent;
    }
    public function getCourseName() {
        return explode('||', $this->model->getName())[1];
    }
    public function getCourseCode() {
        return explode('||', $this->model->getName())[0];
    }
    public function getFullUrl($path = null) {
        // Check if we already have the url fetched
        if ($this->fullUrl === null) {
            // Not fetched, generate
            if ($this->model->isLink()) {
                $this->fullUrl = $this->model->getId();
            }

            // Check if already loaded (or cached)
            if ($this->fullUrl === null) {
                $temp_element = $this->model;
                $temp_url = [$this->model->getUrlFriendly()];

                while (true) {
                    if ($temp_element->hasParent()) {
                        $temp_element = $temp_element->getParent(true);
                    }
                    else {
                        break;
                    }

                    $temp_url[] = $temp_element->getUrlFriendly();
                }

                $this->fullUrl = implode('/', array_reverse($temp_url))  . ($this->model->isDirectory() ? '/' : '');
            }
        }

        // If we dont have any path, just return the full url
        if ($path === null) {
            return $this->fullUrl;
        }

        // Return url with path here
        return substr($path, 1) . '/' . $this->fullUrl;
    }
    public function setFullUrl($url) {
        $this->fullUrl = $url;
    }
}