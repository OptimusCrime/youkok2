<?php
/*
 * File: ElmentController.php
 * Holds: Controller for the model Element
 * Created: 06.11.2014
 * Project: Youkok2
 *
 */

namespace Youkok2\Models\Controllers;

use Youkok2\Models\Download;
use Youkok2\Models\Element;
use Youkok2\Models\Me;
use Youkok2\Utilities\Routes;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\TemplateHelper;

class ElementController extends BaseController
{
    
    /*
     * Variables
     */
    
    public static $cacheKey = 'i';

    // Additional fields in cache
    private $parent;
    private $rootParent;
    private $fullUrl;
    private $parents;
    private $aliasFor;


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
    
    public static $timeIntervals = [
        'mysql' => [
            // All
            'WHERE a.pending = 0 AND a.deleted = 0',

            // Day
            'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND a.pending = 0 AND a.deleted = 0',

            // Week
            'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND a.pending = 0 AND a.deleted = 0',

            // Month
            'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND a.pending = 0 AND a.deleted = 0',

            // Year
            'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 YEAR) AND a.pending = 0 AND a.deleted = 0',
        ],
        'sqlite' => [
            // All
            'WHERE a.pending = 0 AND a.deleted = 0',

            // Day
            'WHERE d.downloaded_time >= datetime("now", "-1 day") AND a.pending = 0 AND a.deleted = 0',

            // Week
            'WHERE d.downloaded_time >= datetime("now", "-7 days")  AND a.pending = 0 AND a.deleted = 0',

            // Month
            'WHERE d.downloaded_time >= datetime("now", "-1 month")  AND a.pending = 0 AND a.deleted = 0',

            // Year
            'WHERE d.downloaded_time >= datetime("now", "-1 year")  AND a.pending = 0 AND a.deleted = 0',
        ]
    ];
    
    /*
     * Constructor
     */
    
    public function __construct($model) {
        parent::__construct($this, $model);

        // Parents and children
        $this->parents = null;

        // Other stuff
        $this->flagCount = null;
        $this->downloadCount = [0 => null, 1 => null, 2 => null, 3 => null];
        
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
        $url_pieces = [];
        foreach ($url_pieces_temp as $k => $v) {
            if ($v != 'last-ned' and $v != 'redirect' and strlen($v) > 0) {
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
                $get_reverse_url  = "SELECT id, alias" . PHP_EOL;
                $get_reverse_url .= "FROM archive " . PHP_EOL;
                
                if ($temp_parent === null) {
                    $get_reverse_url .= "WHERE parent IS NULL" . PHP_EOL;
                    $reverse_arr = [':url_friendly' => $url_piece_single];
                }
                else {
                    $get_reverse_url .= "WHERE parent = :parent" . PHP_EOL;
                    $reverse_arr = [':parent' => $temp_parent,
                        ':url_friendly' => $url_piece_single];
                }
                
                $get_reverse_url .= "AND url_friendly = :url_friendly" . PHP_EOL;
                $get_reverse_url .= "AND pending = 0" . PHP_EOL;
                $get_reverse_url .= "AND deleted = 0";
                
                $get_reverse_url_query = Database::$db->prepare($get_reverse_url);
                $get_reverse_url_query->execute($reverse_arr);
                $row = $get_reverse_url_query->fetch(\PDO::FETCH_ASSOC);
                
                // Check if anything was returned
                if (isset($row['id'])) {
                    // Check if this is the last element
                    if ($k == ($num_pieces - 1)) {
                        $element_id = $row['id'];
                        
                        // Make sure to handle aliases if any are found
                        if ($temp_parent == null and $row['alias'] !== null) {
                            // Use the alias id instead
                            $element_id = $row['alias'];
                        }
                        
                        // Last element, just use createById
                        $this->createById($element_id);
                    }
                    else {
                        // Make sure to handle aliases if any are found
                        if ($temp_parent == null and $row['alias'] !== null) {
                            // Use the alias id instead
                            $temp_parent = $row['alias'];
                        }
                        else {
                            // Was found, update the current id
                            $temp_parent = $row['id'];
                        }
                        
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
        if ($this->model->getId() != null and is_numeric($this->model->getId())) {
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
            $get_children_ids .= "AND pending = 0" . PHP_EOL;
            $get_children_ids .= "AND deleted = 0" . PHP_EOL;
            $get_children_ids .= "ORDER BY directory DESC, name ASC";
            
            $get_children_ids_query = Database::$db->prepare($get_children_ids);
            $get_children_ids_query->execute([':parent' => $this->model->getId()]);
            while ($row = $get_children_ids_query->fetch(\PDO::FETCH_ASSOC)) {
                // Get element and add to collection
                $this->children[] = Element::get($row['id']);
            }
        }

        // Return the children collection
        return $this->children;
    }

    /*
     * Download methods
     */
    
    public function getDownloadCount($delta) {
        // Get the delta index
        $index = 0;
        foreach (self::$timeIntervals[DATABASE_ADAPTER] as $k => $v) {
            if ($k == $delta) {
                $index = $k;
                break;
            }
        }
        
        // Check if this value is already cached
        if ($this->downloadCount[$index] == null) {
            // This count is not cached, run query to fetch the download number
            $get_download_count  = "SELECT COUNT(d.id) as 'downloaded_times'" . PHP_EOL;
            $get_download_count .= "FROM download d" . PHP_EOL;
            $get_download_count .= "LEFT JOIN archive AS a ON a.id = d.file" . PHP_EOL;
            $get_download_count .= self::$timeIntervals[DATABASE_ADAPTER][$index] . PHP_EOL;
            $get_download_count .= "AND d.file = :file";
            
            $get_download_count_query = Database::$db->prepare($get_download_count);
            $get_download_count_query->execute([':file' => $this->model->getId()]);
            $row = $get_download_count_query->fetch(\PDO::FETCH_ASSOC);

            // Set value
            $this->downloadCount[$index] = $row['downloaded_times'];
        }

        // Return the result
        return $this->downloadCount[$index];
    }
    
    public function setDownloadCount($delta, $value) {
        $this->downloadCount[$delta] = $value;
    }

    public function addDownload($me) {
        // New instance for download
        $download = new Download();

        // Set values
        $download->setFile($this->model->getId());
        $download->setIp($_SERVER['REMOTE_ADDR']);
        $download->setAgent($_SERVER['HTTP_USER_AGENT']);

        // Check if user is logged in
        if ($me != null && $me->isLoggedIn()) {
            $download->setUser($me->getId());
        }

        // Save the object
        $download->save();
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
        $get_all_flags_query->execute([':file' => $this->id]);
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
            $this->flags = [];
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
            $get_owner_query->execute([':file' => $this->id]);
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
     * Get graph data TODO, this does not work any more
     */
    
    public function getGraphData($type = null) {
        // Set type if null
        if ($type === null) {
            $type = 0;
        }
        
        // Some variables
        $output = [];
        $previous_num = 0;
        
        // The query
        $get_all_downloads  = "SELECT COUNT(id) AS 'num', downloaded_time" . PHP_EOL;
        $get_all_downloads .= "FROM download" . PHP_EOL;
        $get_all_downloads .= "WHERE file = :file" . PHP_EOL;
        $get_all_downloads .= "GROUP BY TO_DAYS(downloaded_time)" . PHP_EOL;
        $get_all_downloads .= "ORDER BY downloaded_time ASC";
        
        $get_all_downloads_query = $this->controller->db->prepare($get_all_downloads);
        $get_all_downloads_query->execute([':file' => $this->id]);
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
            $output[] = ['Date.UTC(' . $date_split[0] . ', ' . $date_split[1] . ', ' . $date_split[2] . ', ' .
                $time_split[0] . ', ' . $time_split[1] . ', ' . $time_split[2] . ')',
                              $num_count];
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
     * To Array (for output)
     */

    public function toArray($nest = true) {
        // Get the initial fields from the array
        $arr = $this->model->toArrayInitial();
        
        // Get added pretty
        $arr['added_pretty'] = $this->model->getAdded(true);
        
        // Swap name for course information for courses
        if (!$this->hasParent()) {
            $arr['course_code'] = $this->getCourseCode();
            $arr['course_name'] = $this->getCourseName();
            unset($arr['name']);
        }
        
        // Add download count if present
        foreach ($this->downloadCount as $v) {
            if ($v > 0) {
                $arr['download_count'] = $v;
            }
        }

        // Check if we should nest (applying parents)
        if ($nest) {
            // Check if the object has some parents at all
            if ($this->hasParent()) {
                $arr['parents'] = [];

                // Check if the parent is the root object or if we have multiple depth
                $root_parent = $this->getRootParent();
                if ($root_parent->getId() != $this->model->getParent()) {
                    $arr['parents'][] = $this->model->getParent(true)->toArray(false);
                }

                // Add the root parent
                $arr['parents'][] = $root_parent->toArray(false);
            }
        }

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
        return htmlspecialchars(explode('||', $this->model->getName())[1]);
    }
    public function getCourseCode() {
        return htmlspecialchars(explode('||', $this->model->getName())[0]);
    }
    public function getFullUrl() {
        // Check if we already have the url fetched
        if ($this->fullUrl === null) {
            // Not fetched, generate
            $temp_url = [];

            // Check if this object is a link
            if ($this->model->isLink()) {
                $temp_url[] = $this->model->getId();
            }

            // Check if already loaded (or cached)
            if (count($temp_url) == 0) {
                $temp_element = $this->model;
                $temp_url[] = $this->model->getUrlFriendly();

                while (true) {
                    if ($temp_element->hasParent()) {
                        $temp_element = $temp_element->getParent(true);
                    }
                    else {
                        break;
                    }

                    $temp_url[] = $temp_element->getUrlFriendly();
                }
            }

            // Find the correct prefix
            $url_fragments = array_reverse($temp_url);
            $path = TemplateHelper::urlFor('download', $url_fragments);
            if ($this->model->isLink()) {
                $path = TemplateHelper::urlFor('redirect', $url_fragments);
            }
            elseif ($this->model->isDirectory()) {
                $path = TemplateHelper::urlFor('archive', $url_fragments);
            }

            // Generate the final url
            $this->fullUrl = $path;
        }

        // Return the final url here
        return $this->fullUrl;
    }
    public function setFullUrl($url) {
        $this->fullUrl = $url;
    }
    public function getAliasFor() {
        // Check if we already have the aliases fetched
        if ($this->aliasFor === null) {
            $aliases = [];
            
            $get_alias_elements  = "SELECT id" . PHP_EOL;
            $get_alias_elements .= "FROM archive" . PHP_EOL;
            $get_alias_elements .= "WHERE alias = :alias";
            
            $get_alias_elements_query = Database::$db->prepare($get_alias_elements);
            $get_alias_elements_query->execute([':alias' => $this->model->getId()]);
            while ($row = $get_alias_elements_query->fetch(\PDO::FETCH_ASSOC)) {
                // Make sure alias owner is a valid and visible element
                $element = Element::get($row['id']);
                
                if ($element->wasFound() and !$element->isPending()) {
                    $aliases[] = $row['id'];
                }
            }
            
            // Add the alias
            $this->setAliasFor($aliases);
        }
        
        // Return the list of aliases
        return $this->aliasFor;
    }
    public function setAliasFor($aliases) {
        $this->aliasFor = $aliases;
    }
    
    public function updateLastVisited() {
        $last_visited = date("Y-m-d H:i:s", time());
        
        $update_last_visited  = "UPDATE archive" . PHP_EOL;
        $update_last_visited .= "SET last_visited = :last_visited" . PHP_EOL;
        $update_last_visited .= "WHERE id = :id";
        
        $result = Database::$db->prepare($update_last_visited);
        $result->execute([
            ':last_visited' => $last_visited,
            ':id' => $this->model->getId(),
        ]);
        
        $this->model->setLastVisited($last_visited);
    }
}
